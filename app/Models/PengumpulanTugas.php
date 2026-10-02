<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'dikirim_oleh',
        'jumlah_progres',
        'tautan_berkas',
        'catatan_kendala',
        'status_verifikasi',
        'catatan_ketua',
        'dikirim_pada',
    ];

    protected function casts(): array
    {
        return [
            'dikirim_pada' => 'datetime',
            'jumlah_progres' => 'integer',
        ];
    }

    /**
     * Tugas terkait pengumpulan ini.
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    /**
     * User yang mengirim pengumpulan ini.
     */
    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikirim_oleh');
    }

    /**
     * Label status verifikasi dengan warna badge.
     */
    public function getBadgeVerifikasiAttribute(): array
    {
        return match ($this->status_verifikasi) {
            'menunggu'     => ['label' => 'Menunggu Review', 'class' => 'bg-amber-50 text-amber-700'],
            'disetujui'    => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
            'perlu_revisi' => ['label' => 'Perlu Revisi', 'class' => 'bg-red-50 text-red-700'],
            default        => ['label' => $this->status_verifikasi, 'class' => 'bg-slate-100 text-slate-600'],
        };
    }
}
