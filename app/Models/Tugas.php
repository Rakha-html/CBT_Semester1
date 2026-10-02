<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'divisi_id',
        'dibuat_oleh',
        'ditugaskan_ke',
        'judul_tugas',
        'deskripsi',
        'tipe_target',
        'prioritas',
        'status',
        'progres_persen',
        'target_jumlah',
        'jumlah_tercapai',
        'tenggat_waktu',
    ];

    protected function casts(): array
    {
        return [
            'tenggat_waktu' => 'datetime',
            'progres_persen' => 'integer',
            'target_jumlah' => 'integer',
            'jumlah_tercapai' => 'integer',
        ];
    }

    /**
     * Divisi pemilik tugas ini.
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    /**
     * User yang membuat tugas ini.
     */
    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * User PIC (Person In Charge) yang ditugaskan.
     */
    public function penanggungjawab(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditugaskan_ke');
    }

    /**
     * Pengumpulan/submission terkait tugas ini.
     */
    public function pengumpulan(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }

    /**
     * Riwayat aktivitas terkait tugas ini.
     */
    public function riwayatAktivitas(): HasMany
    {
        return $this->hasMany(RiwayatAktivitas::class, 'tugas_id');
    }

    /**
     * Hitung persentase capaian tugas (dalam satuan %).
     */
    public function getPersentaseCapaianAttribute(): float
    {
        if ($this->status === 'selesai' || $this->status === 'sudah_dikerjakan') {
            return 100.0;
        }

        if ($this->progres_persen !== null && $this->progres_persen > 0) {
            return (float) $this->progres_persen;
        }

        if ($this->target_jumlah && $this->target_jumlah > 0) {
            return min(100.0, round(($this->jumlah_tercapai / $this->target_jumlah) * 100, 1));
        }

        return 0.0;
    }

    /**
     * Label prioritas dengan warna badge.
     */
    public function getBadgePrioritasAttribute(): array
    {
        return match ($this->prioritas) {
            'rendah'   => ['label' => 'Rendah', 'class' => 'bg-slate-100 text-slate-600'],
            'sedang'   => ['label' => 'Sedang', 'class' => 'bg-blue-100 text-blue-700'],
            'tinggi'   => ['label' => 'Tinggi', 'class' => 'bg-amber-100 text-amber-700'],
            'mendesak' => ['label' => 'Mendesak', 'class' => 'bg-red-100 text-red-700'],
            default    => ['label' => $this->prioritas, 'class' => 'bg-slate-100 text-slate-600'],
        };
    }

    /**
     * Label status kategori dengan warna badge.
     * Sesuai kebutuhan: Belum Dikerjakan, Sedang Dikerjakan, Sudah Dikerjakan.
     */
    public function getBadgeStatusAttribute(): array
    {
        return match ($this->status) {
            'belum_dikerjakan'            => ['label' => 'Belum Dikerjakan', 'class' => 'bg-slate-100 text-slate-600 border border-slate-200'],
            'sedang_dikerjakan'           => ['label' => 'Sedang Dikerjakan', 'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
            'selesai', 'sudah_dikerjakan' => ['label' => 'Sudah Dikerjakan', 'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
            default                       => ['label' => $this->status, 'class' => 'bg-slate-100 text-slate-600'],
        };
    }
}
