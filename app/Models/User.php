<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;



class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'user_type',
        'status',
        'password',
        'phone_number',
        'nomor_str',      // <-- TAMBAHKAN INI
        'nomor_sip',      // <-- TAMBAHKAN INI
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function userProfile() {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }
    
    public function organisasis(): BelongsToMany
    {
        return $this->belongsToMany(Organisasi::class, 'organisasi_user');
    }
    /**
     * Relasi one-to-one ke model DokterProfile.
     */
    public function dokterProfile()
    {
        return $this->hasOne(DokterProfile::class);
    }

    public function getAccessibleOrganisasiIds(): array
    {
        // 1. Jika user adalah admin, berikan akses ke semua organisasi.
        if ($this->hasRole('admin')) {
            return Organisasi::pluck('id')->toArray();
        }

        // 2. Ambil ID organisasi utama yang terhubung langsung dengan user.
        $parentOrgIds = $this->organisasis()->pluck('organisasis.id')->toArray();

        if (empty($parentOrgIds)) {
            return [];
        }

        $allOrgIds = $parentOrgIds;
        $idsToSearch = $parentOrgIds;

        // 3. Lakukan loop untuk mencari semua anak dari organisasi di atas secara efisien.
        while (!empty($idsToSearch)) {
            $childIds = Organisasi::whereIn('parent_id', $idsToSearch)->pluck('id')->toArray();
            
            if (empty($childIds)) {
                break; // Hentikan jika tidak ada anak lagi yang ditemukan.
            }
            
            $allOrgIds = array_merge($allOrgIds, $childIds);
            $idsToSearch = $childIds;
        }

        // 4. Kembalikan array ID yang unik.
        return array_unique($allOrgIds);
    }

    /**
     * Mengecek apakah user dapat mengakses sebuah organisasi spesifik.
     *
     * @param int $organisasiId
     * @return bool
     */
    public function canAccessOrganisasi(int $organisasiId): bool
    {
        // Admin selalu bisa mengakses
        if ($this->hasRole('admin')) {
            return true;
        }

        // Cek apakah ID organisasi yang diminta ada di dalam daftar ID yang bisa diakses user
        return in_array($organisasiId, $this->getAccessibleOrganisasiIds());
    }

    /**
     * Mendapatkan semua data konsultasi yang dilakukan oleh user ini (sebagai dokter).
     */
    public function konsultasisSebagaiDokter()
    {
        return $this->hasMany(Konsultasi::class, 'dokter_id');
    }    
}
