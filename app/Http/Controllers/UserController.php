<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Organisasi;
use App\Models\DokterProfile;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $currentUser = Auth::user();
            
            // Variabel untuk filter, diinisialisasi kosong
            $roles_for_filter = [];
            $organisasis_for_filter = [];

            // Ambil data untuk dropdown filter (hanya untuk admin)
            if ($currentUser->hasRole('admin')) {
                $roles_for_filter = Role::where('name', '!=', 'super-admin')->pluck('name', 'name');
                $organisasis_for_filter = Organisasi::whereNull('parent_id')->pluck('nama_organisasi', 'id');
            }

            if ($this->shouldShowWarning($currentUser)) {
                $emptyUsers = new LengthAwarePaginator([], 0, 10);
                return view('users.index', [
                    'showWarning' => true, 
                    'users' => new LengthAwarePaginator([], 0, 10),
                    'sortBy' => 'first_name',
                    'sortDirection' => 'asc',
                    'roles_for_filter' => $roles_for_filter,
                    'organisasis_for_filter' => $organisasis_for_filter,
                ]);
            }

            // --- LOGIKA SORTING (Tidak berubah) ---
            $sortBy = $request->input('sort_by', 'first_name'); 
            $sortDirection = $request->input('sort_direction', 'asc');
            $sortableColumns = ['id','first_name', 'email'];
            if (!in_array($sortBy, $sortableColumns)) {
                $sortBy = 'first_name';
            }
            
            // Ambil query dasar
            $usersQuery = $this->getUsersQuery($currentUser);

            // --- LOGIKA FILTER BARU (Hanya untuk admin) ---
            if ($currentUser->hasRole('admin')) {
                if ($request->filled('role')) {
                    $usersQuery->whereHas('roles', function ($query) use ($request) {
                        $query->where('name', $request->role);
                    });
                }
                if ($request->filled('organisasi_id')) {
                    $usersQuery->whereHas('organisasis', function ($query) use ($request) {
                        $query->where('organisasi_id', $request->organisasi_id);
                    });
                }
            }
            
            // Terapkan sorting dan paginasi
            $users = $usersQuery->orderBy($sortBy, $sortDirection)->paginate(10);
            
            // Kirim semua variabel yang dibutuhkan ke view
            return view('users.index', compact('users', 'sortBy', 'sortDirection', 'roles_for_filter', 'organisasis_for_filter'));
            
        } catch (\Exception $e) {
            Log::error('Error in UserController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data pengguna.');
        }
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $currentUser = Auth::user();
            
            $roles = $this->getAvailableRoles($currentUser);
            $organisasis = $this->getAccessibleOrganisasis($currentUser);
            
            return view('users.create', compact('roles', 'organisasis'));
            
        } catch (\Exception $e) {
            Log::error('Error in UserController@create: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan saat memuat form.');
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $currentUser = Auth::user();
            $validatedData = $this->validateUserData($request);
            
            // Authorization check for koorUser
            $this->authorizeOrganisasiAssignment($currentUser, $validatedData['organisasi_ids']);
            
            // Create user
            $user = $this->createUser($validatedData);
            
            // Create user profile
            $this->createUserProfile($user, $validatedData);
            
            // Assign role and organizations
            $this->assignRoleAndOrganizations($user, $validatedData);
            
            // Create doctor profile if needed
            $this->createDokterProfileIfNeeded($user, $validatedData);
            
            DB::commit();
            
            Log::info("User created successfully", ['user_id' => $user->id, 'created_by' => $currentUser->id]);
            
            return redirect()->route('users.index')
                ->with('success', 'Pengguna baru berhasil ditambahkan.');
                
            } catch (ValidationException $e) {
                // Biarkan Laravel yang menangani redirect untuk error validasi
                DB::rollBack();
                throw $e;
            } catch (\Exception $e) {
                // Tangani semua error lain yang mungkin terjadi
                DB::rollBack();
                Log::error('Error in UserController@store: ' . $e->getMessage());
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = User::with('userProfile', 'roles')->findOrFail($id);
            $profileImage = getSingleMedia($data, 'profile_image');
            
            return view('users.profile', compact('data', 'profileImage'));
            
        } catch (\Exception $e) {
            Log::error('Error in UserController@show: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Pengguna tidak ditemukan.');
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        try {
            $currentUser = Auth::user();
            
            // Authorization check
            $this->authorizeUserEdit($currentUser, $user);
            
            $roles = $this->getAvailableRoles($currentUser);
            $organisasis = $this->getAccessibleOrganisasis($currentUser);
            
            $user->load('organisasis', 'roles');
            
            return view('users.edit', compact('user', 'roles', 'organisasis'));
            
        } catch (\Exception $e) {
            Log::error('Error in UserController@edit: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan saat memuat form edit.');
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();
        
        try {
            $currentUser = Auth::user();
            
            // Authorization check
            $this->authorizeUserUpdate($currentUser, $user);
            
            // Validate request data
            $validatedData = $this->validateUserUpdateData($request, $user);
            
            // Update user basic data
            $this->updateUserBasicData($user, $validatedData);
            
            // Update user profile
            $this->updateUserProfile($user, $validatedData);
            
            // Update role and organizations (only for admin/koorUser)
            if ($currentUser->hasAnyRole(['admin', 'koorUser'])) {
                $this->updateRoleAndOrganizations($currentUser, $user, $validatedData);
                $this->updateDokterProfile($user, $validatedData);
            }
            
            DB::commit();
            
            Log::info("User updated successfully", ['user_id' => $user->id, 'updated_by' => $currentUser->id]);
            
            return redirect()->route('users.index')
                ->with('success', 'Data pengguna berhasil diperbarui.');
                
            } catch (ValidationException $e) {
                // Biarkan Laravel yang menangani redirect untuk error validasi
                DB::rollBack();
                throw $e;
            } catch (\Exception $e) {
                // Tangani semua error lain yang mungkin terjadi
                DB::rollBack();
                Log::error('Error in UserController@update: ' . $e->getMessage());
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $currentUser = Auth::user();
            
            // Prevent self-deletion
            if ($currentUser->id === $user->id) {
                return redirect()->route('users.index')
                    ->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
            }
            
            // Only admin can delete users
            if (!$currentUser->hasRole('admin')) {
                return redirect()->route('users.index')
                    ->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini.');
            }
            
            $user->delete();
            
            Log::info("User deleted successfully", ['user_id' => $user->id, 'deleted_by' => $currentUser->id]);
            
            return redirect()->route('users.index')
                ->with('success', 'Pengguna berhasil dihapus.');
                
        } catch (\Exception $e) {
            Log::error('Error in UserController@destroy: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan saat menghapus pengguna.');
        }
    }

    // ========================================================================
    // PRIVATE HELPER METHODS
    // ========================================================================

    /**
     * Check if should show warning for non-admin users without organization.
     */
    private function shouldShowWarning($currentUser): bool
    {
        return !$currentUser->hasRole('admin') && $currentUser->organisasis()->count() === 0;
    }

    /**
     * Get users query based on current user permissions.
     */
    private function getUsersQuery($currentUser)
    {
        $usersQuery = User::with('roles', 'organisasis');

        if (!$currentUser->hasRole('admin')) {
            $accessibleOrgIds = $currentUser->getAccessibleOrganisasiIds();
            
            $usersQuery->whereHas('organisasis', function ($query) use ($accessibleOrgIds) {
                $query->whereIn('organisasi_user.organisasi_id', $accessibleOrgIds);
            });
        }

        return $usersQuery;
    }

    /**
     * Get available roles based on current user permissions.
     */
    private function getAvailableRoles($currentUser)
    {
        if ($currentUser->hasRole('admin')) {
            return Role::where('name', '!=', 'super-admin')->pluck('name', 'name');
        }
        
        return Role::whereIn('name', ['user', 'dokter', 'koorUser'])->pluck('name', 'name');
    }

    /**
     * Get accessible organizations based on current user permissions.
     */
    private function getAccessibleOrganisasis($currentUser)
    {
        if ($currentUser->hasRole('admin')) {
            // Tampilkan semua organisasi yang merupakan parent
            return Organisasi::whereNull('parent_id') // <-- TAMBAHKAN INI
                ->orderBy('nama_organisasi', 'asc')
                ->pluck('nama_organisasi', 'id');
        }
        
        // Untuk non-admin, tampilkan organisasi yang ditugaskan kepada mereka DAN merupakan parent
        $organisasiIds = $currentUser->organisasis->pluck('id');
        return Organisasi::whereIn('id', $organisasiIds)
            ->whereNull('parent_id') // <-- TAMBAHKAN INI
            ->orderBy('nama_organisasi', 'asc')
            ->pluck('nama_organisasi', 'id');
    }

    /**
     * Validate user data for creation.
     */
    private function validateUserData(Request $request): array
    {
        // Ambil daftar role yang tersedia untuk user saat ini
        $availableRoles = $this->getAvailableRoles(Auth::user());
        // Buat aturan validasi 'in' secara dinamis
        $roleValidationRule = 'required|in:' . implode(',', $availableRoles->keys()->toArray());

        return $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => $roleValidationRule,
            'organisasi_ids' => 'required|array',
            'organisasi_ids.*' => 'exists:organisasis,id',
            'nomor_str' => 'nullable|string|required_if:role,dokter',
            'nomor_sip' => 'nullable|string|required_if:role,dokter',
            'userProfile.gender' => 'required|in:L,P',
            'userProfile.phone_number' => 'nullable|numeric|digits_between:10,13',
        ]);
    }

    /**
     * Validate user data for update.
     */
    private function validateUserUpdateData(Request $request, User $user): array
    {
    // Ambil daftar role yang tersedia untuk user saat ini
    $availableRoles = $this->getAvailableRoles(Auth::user());
    // Buat aturan validasi 'in' secara dinamis
    $roleValidationRule = 'sometimes|required|in:' . implode(',', $availableRoles->keys()->toArray());
            
        return $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => $roleValidationRule,
            'organisasi_ids' => 'sometimes|required|array',
            'organisasi_ids.*' => 'exists:organisasis,id',
            'nomor_str' => 'nullable|string|required_if:role,dokter',
            'nomor_sip' => 'nullable|string|required_if:role,dokter',
            'userProfile.gender' => 'required|in:L,P',
            'userProfile.phone_number' => 'nullable|numeric|digits_between:10,13', 
        ]);
    }

    /**
     * Authorize organization assignment for koorUser.
     */
    private function authorizeOrganisasiAssignment($currentUser, array $organisasiIds): void
    {
        if ($currentUser->hasRole('koorUser')) {
            $allowedOrgIds = $currentUser->organisasis()
                ->whereNull('parent_id')
                ->pluck('id')
                ->toArray();
                
            foreach ($organisasiIds as $requestedOrgId) {
                if (!in_array($requestedOrgId, $allowedOrgIds)) {
                    abort(403, 'Anda tidak berwenang menugaskan pengguna ke organisasi tersebut.');
                }
            }
        }
    }

    /**
     * Authorize user edit access.
     */
    private function authorizeUserEdit($currentUser, User $user): void
    {
        if (!$currentUser->hasRole('admin') && $currentUser->id !== $user->id) {
            $userOrgIds = $user->organisasis->pluck('id')->toArray();
            $isWithinScope = !empty(array_intersect($currentUser->getAccessibleOrganisasiIds(), $userOrgIds));
            
            if (!$currentUser->hasRole('koorUser') || !$isWithinScope) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit pengguna ini.');
            }
        }
    }

    /**
     * Authorize user update access.
     */
    private function authorizeUserUpdate($currentUser, User $user): void
    {
        if (!$currentUser->hasRole('admin') && $currentUser->id !== $user->id) {
            $isWithinScope = !empty(array_intersect(
                $currentUser->getAccessibleOrganisasiIds(), 
                $user->getAccessibleOrganisasiIds()
            ));
            
            if (!$currentUser->hasRole('koorUser') || !$isWithinScope) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah pengguna ini.');
            }
        }
    }

    /**
     * Create new user.
     */
    private function createUser(array $validatedData): User
    {
        return User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'status' => 'active',
        ]);
    }

    /**
     * Create user profile.
     */
    private function createUserProfile(User $user, array $validatedData): void
    {
        // Ambil semua data dari array 'userProfile' yang sudah divalidasi
        $profileData = $validatedData['userProfile']; 
        
        $user->userProfile()->create($profileData);
    }

    /**
     * Update user profile.
     */
    private function updateUserProfile(User $user, array $validatedData): void
    {
        // Cek apakah data userProfile ada di hasil validasi sebelum diupdate
        if (isset($validatedData['userProfile'])) {
            $profileData = $validatedData['userProfile'];
            
            $user->userProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }
    }

    /**
     * Assign role and organizations to user.
     */
    private function assignRoleAndOrganizations(User $user, array $validatedData): void
    {
        $user->assignRole($validatedData['role']);
        $user->organisasis()->sync($validatedData['organisasi_ids']);
    }

    /**
     * Create doctor profile if user role is dokter.
     */
    private function createDokterProfileIfNeeded(User $user, array $validatedData): void
    {
        if ($validatedData['role'] === 'dokter') {
            DokterProfile::create([
                'user_id' => $user->id,
                'nomor_str' => $validatedData['nomor_str'],
                'nomor_sip' => $validatedData['nomor_sip'],
            ]);
        }
    }

    /**
     * Update user basic data.
     */
    private function updateUserBasicData(User $user, array $validatedData): void
    {
        $userData = [
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email']
        ];
        
        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }
        
        $user->update($userData);
    }

    /**
     * Update role and organizations (for admin/koorUser only).
     */
    private function updateRoleAndOrganizations($currentUser, User $user, array $validatedData): void
    {
        if ($currentUser->hasRole('koorUser') && isset($validatedData['organisasi_ids'])) {
            $this->authorizeOrganisasiAssignment($currentUser, $validatedData['organisasi_ids']);
        }

        if ($currentUser->hasRole('admin') && isset($validatedData['role'])) {
            $user->syncRoles([$validatedData['role']]);
        }
        
        if (isset($validatedData['organisasi_ids'])) {
            $user->organisasis()->sync($validatedData['organisasi_ids']);
        }
    }

    /**
     * Update doctor profile.
     */
    private function updateDokterProfile(User $user, array $validatedData): void
    {
        if (isset($validatedData['role'])) {
            if ($validatedData['role'] === 'dokter') {
                $user->dokterProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nomor_str' => $validatedData['nomor_str'],
                        'nomor_sip' => $validatedData['nomor_sip']
                    ]
                );
            } else {
                $user->dokterProfile()->delete();
            }
        }
    }

    /**
     * Template excel untuk import Excel.
     */
    public function downloadTemplate()
    {
        $path = public_path('templates/template_import_user.xlsx');
        // PASTIKAN Anda sudah membuat file template ini di public/templates/
        $headers = ['Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        return response()->download($path, 'template_import_user.xlsx', $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // Buat instance dari importer terlebih dahulu
        $import = new UsersImport;

        try {
            // Jalankan impor menggunakan instance
            Excel::import($import, $request->file('file'));
            
            // Ambil hasil dari importer
            $successCount = $import->getImportedCount();
            $successNames = implode(', ', $import->getImportedNames());

            if ($successCount > 0) {
                $message = "Impor berhasil! <strong>{$successCount}</strong> pengguna berhasil ditambahkan: {$successNames}.";
                return redirect()->route('users.index')->with('success', $message);
            } else {
                return redirect()->route('users.index')->with('success', 'Tidak ada pengguna baru yang diimpor.');
            }

        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                // Gabungkan semua pesan error untuk satu baris
                $errors = implode(', ', $failure->errors());
                // Dapatkan nama dari baris yang gagal jika ada, atau gunakan nomor baris
                $rowIdentifier = $failure->values()['nama_depan'] ?? 'Baris ' . $failure->row();
                $errorMessages[] = "<strong>{$rowIdentifier}</strong>: {$errors}";
            }
            return redirect()->route('users.index')->with('error', "Impor gagal karena validasi. Detail:<br>" . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', 'Terjadi kesalahan sistem saat mengimpor file: ' . $e->getMessage());
        }
    }

}