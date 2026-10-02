<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'divisi_id',
        'nama_lengkap',
        'email',
        'password',
        'nomor_telepon',
        'peran',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Apakah user ini adalah Ketua SPMB?
     */
    public function isKetua(): bool
    {
        return $this->peran === 'ketua_spmb';
    }

    /**
     * Apakah user ini adalah Panitia?
     */
    public function isPanitia(): bool
    {
        return $this->peran === 'panitia';
    }

    /**
     * Divisi tempat user ini bertugas.
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    /**
     * Divisi yang dipantau oleh user ini (many-to-many via pemantau_divisi).
     */
    public function divisiDipantau(): BelongsToMany
    {
        return $this->belongsToMany(Divisi::class, 'pemantau_divisi', 'admin_id', 'divisi_id')
                    ->withPivot('ditetapkan_pada');
    }

    /**
     * Tugas yang dibuat oleh user ini.
     */
    public function tugasDibuat(): HasMany
    {
        return $this->hasMany(Tugas::class, 'dibuat_oleh');
    }

    /**
     * Tugas yang ditugaskan ke user ini.
     */
    public function tugasDitugaskan(): HasMany
    {
        return $this->hasMany(Tugas::class, 'ditugaskan_ke');
    }

    /**
     * Pengumpulan tugas yang dikirim oleh user ini.
     */
    public function pengumpulan(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'dikirim_oleh');
    }

    /**
     * Riwayat aktivitas user.
     */
    public function riwayatAktivitas(): HasMany
    {
        return $this->hasMany(RiwayatAktivitas::class, 'admin_id');
    }
}
