<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\PengumpulanTugas;
use App\Models\RiwayatAktivitas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard Ketua SPMB.
     * Menampilkan metrik agregat, chart capaian divisi, dan 5 tugas terbaru.
     */
    public function index()
    {
        // Metrik Agregat
        $totalTugas = Tugas::count();
        $totalTarget = Tugas::whereNotNull('target_jumlah')->sum('target_jumlah');
        $totalCapaian = Tugas::sum('jumlah_tercapai');

        // Persentase Progres Rata-rata Seluruh Tugas
        $persentaseProgres = $totalTugas > 0
            ? round(Tugas::avg('progres_persen'), 1)
            : 0;

        $reviewPending = PengumpulanTugas::where('status_verifikasi', 'menunggu')->count();
        $tugasSelesai = Tugas::whereIn('status', ['selesai', 'sudah_dikerjakan'])->count();

        // Capaian per divisi untuk bar chart
        $divisiCapaian = Divisi::with(['tugas'])->get()->map(function ($divisi) {
            $totalTarget = $divisi->tugas->whereNotNull('target_jumlah')->sum('target_jumlah');
            $totalCapaian = $divisi->tugas->sum('jumlah_tercapai');
            $jumlahTugas = $divisi->tugas->count();
            $tugasSelesai = $divisi->tugas->filter(fn($t) => in_array($t->status, ['selesai', 'sudah_dikerjakan']))->count();
            $avgPersen = $jumlahTugas > 0 ? round($divisi->tugas->avg('progres_persen'), 1) : 0;

            return [
                'nama'          => $divisi->nama_divisi,
                'target'        => $totalTarget,
                'capaian'       => $totalCapaian,
                'persentase'    => $avgPersen,
                'jumlah_tugas'  => $jumlahTugas,
                'tugas_selesai' => $tugasSelesai,
            ];
        });

        // 5 tugas terbaru
        $tugasTerbaru = Tugas::with(['divisi', 'penanggungjawab'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalTugas',
            'persentaseProgres',
            'reviewPending',
            'tugasSelesai',
            'divisiCapaian',
            'tugasTerbaru',
        ));
    }
}
