<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Divisi extends Model
{
    protected $table = 'divisi';

    protected $fillable = [
        'nama_divisi',
        'deskripsi',
    ];

    /**
     * Users yang tergabung dalam divisi ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'divisi_id');
    }

    /**
     * Tugas-tugas yang dialokasikan ke divisi ini.
     */
    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'divisi_id');
    }

    /**
     * User yang memantau divisi ini (many-to-many via pemantau_divisi).
     */
    public function pemantau(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pemantau_divisi', 'divisi_id', 'admin_id')
                    ->withPivot('ditetapkan_pada');
    }
}
