<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; 

class Organisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_organisasi',
        'parent_id',
    ];

    /**
     * Relasi ke parent organisasi
     */
    public function parent()
    {
        return $this->belongsTo(Organisasi::class, 'parent_id');
    }

    /**
     * Relasi ke child organisasi
     */
    public function children()
    {
        return $this->hasMany(Organisasi::class, 'parent_id');
    }

    /**
     * Relasi many-to-many dengan User
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organisasi_user')
                    ->withTimestamps();
    }

    /**
     * Scope untuk mendapatkan hanya organisasi parent
     */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope untuk mendapatkan hanya organisasi child
     */
    public function scopeChildOnly($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Method untuk mendapatkan semua descendant (anak dan cucu, dll)
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

}