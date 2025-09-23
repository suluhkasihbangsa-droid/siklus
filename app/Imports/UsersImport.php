<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Organisasi;
use App\Models\DokterProfile;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets 
{
    private $importedCount = 0;
    private $importedNames = [];
    
    public function sheets(): array
    {
        return [
            // Hanya sheet dengan nama 'import' yang akan diproses oleh class ini
            'import' => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        // Filter baris kosong terlebih dahulu untuk menghindari error
        $filteredRows = $rows->filter(function ($row) {
            // Sebuah baris dianggap tidak kosong jika setidaknya satu kolom wajib diisi
            return !empty($row['username']) && !empty($row['email']);
        });

        if ($filteredRows->isEmpty()) {
            return; // Tidak ada data valid untuk diimpor
        }

        DB::beginTransaction();
        try {
            foreach ($filteredRows as $row) 
            {
                // Lewati baris demo jika ada
                if (isset($row['no']) && $row['no'] == '0') {
                    continue; 
                }

                // 1. Buat User baru (dan langsung isi data dokter jika ada)
                $user = User::create([
                    'first_name'  => $row['nama_depan'],
                    'last_name'   => $row['nama_belakang'],
                    'username'    => $row['username'],
                    'email'       => $row['email'],
                    'password'    => Hash::make($row['password']),
                    'status'      => 'active',
                    // Data dokter langsung disimpan di tabel users
                    'nomor_str'   => strtolower($row['role']) === 'dokter' ? $row['nomor_str'] : null,
                    'nomor_sip'   => strtolower($row['role']) === 'dokter' ? $row['nomor_sip'] : null,
                ]);

                // 2. Buat User Profile
                // Pastikan nomor HP dibersihkan dari karakter non-numerik sebelum disimpan
                $cleanPhoneNumber = preg_replace('/\D/', '', $row['nomor_hp']);
                $user->userProfile()->create([
                    'gender' => strtoupper($row['gender']),
                    'phone_number' => $cleanPhoneNumber,
                ]);

                // 3. Tetapkan Role
                $role = Role::whereRaw('LOWER(name) = ?', [strtolower($row['role'])])->first();
                if ($role) {
                    $user->assignRole($role);
                }

                // 4. Hubungkan ke Organisasi (Logika cerdas sudah bagus!)
                $organisasiIdsToSync = $this->getOrganisasiIdsFromString($row['organisasi']);
                if (!empty($organisasiIdsToSync)) {
                    $user->organisasis()->sync($organisasiIdsToSync);
                }
                
                // HAPUS LOGIKA DUPLIKAT DOKTER PROFILE
                // Data dokter sekarang sudah ada di tabel 'users'

                // Hitung data yang berhasil
                $this->importedCount++;
                $this->importedNames[] = $user->first_name;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            '*.no'           => 'nullable|numeric',
            '*.nama_depan'   => 'required|string',
            '*.nama_belakang'=> 'required|string',
            '*.username'     => 'required|string|unique:users,username',
            '*.email'        => 'required|email|unique:users,email',
            '*.password'     => 'required|string|min:8',
            '*.gender'       => 'required|in:L,P,l,p',
            // Validasi Nomor HP sebagai string yang hanya berisi angka
            '*.nomor_hp'     => 'nullable|string|regex:/^[0-9]+$/|min:10|max:13',
            '*.role'         => 'required|string|exists:roles,name',
            '*.organisasi'   => 'nullable|string',
            '*.nomor_str'      => 'nullable|string',
            '*.nomor_sip'      => 'nullable|string',
        ];
    }
    
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getImportedNames(): array
    {
        return $this->importedNames;
    }

    /**
     * Mengambil ID organisasi dari string, membuat organisasi baru jika belum ada.
     * Mendukung format "Parent > Child" atau "Parent".
     *
     * @param string|null $organisasiString
     * @return array
     */
    private function getOrganisasiIdsFromString($organisasiString): array
    {
        if (empty($organisasiString)) {
            return [];
        }

        $organisasiIdsToSync = [];
        $organisasiEntries = array_map('trim', explode(',', $organisasiString));

        foreach ($organisasiEntries as $entry) {
            if (strpos($entry, '>') !== false) {
                // Kasus: "Parent > Child"
                $parts = array_map('trim', explode('>', $entry));
                $parentName = $parts[0];
                $childName = $parts[1] ?? null;

                if ($parentName && $childName) {
                    $parentOrg = Organisasi::firstOrCreate(
                        ['nama_organisasi' => $parentName, 'parent_id' => null]
                    );
                    
                    $childOrg = Organisasi::firstOrCreate(
                        ['nama_organisasi' => $childName, 'parent_id' => $parentOrg->id]
                    );

                    $organisasiIdsToSync[] = $childOrg->id;
                }
            } else {
                // Kasus: "Parent" saja
                $org = Organisasi::firstOrCreate(
                    ['nama_organisasi' => $entry, 'parent_id' => null]
                );
                $organisasiIdsToSync[] = $org->id;
            }
        }
        
        return $organisasiIdsToSync;
    }

}