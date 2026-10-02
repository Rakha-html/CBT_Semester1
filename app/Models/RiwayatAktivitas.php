<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatAktivitas extends Model
{
    protected $table = 'riwayat_aktivitas';

    /**
     * Audit trail hanya punya created_at, tidak ada updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'tugas_id',
        'admin_id',
        'jenis_aktivitas',
        'keterangan',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Tugas terkait aktivitas ini.
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * User yang melakukan aktivitas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
