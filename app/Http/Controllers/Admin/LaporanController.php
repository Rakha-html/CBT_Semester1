<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Halaman ekspor LPJ - rekapitulasi data siap cetak.
     */
    public function index(Request $request)
    {
        $query = Tugas::with(['divisi', 'penanggungjawab', 'pengumpulan']);

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        $tugas = $query->orderBy('divisi_id')->orderBy('created_at', 'desc')->get();

        // Rangkuman statistik
        $totalTugas = $tugas->count();
        $tugasSelesai = $tugas->filter(fn($t) => in_array($t->status, ['selesai', 'sudah_dikerjakan']))->count();
        $tugasBerjalan = $tugas->where('status', 'sedang_dikerjakan')->count();
        $tugasBelum = $tugas->where('status', 'belum_dikerjakan')->count();

        $divisiList = Divisi::orderBy('nama_divisi')->get();

        return view('admin.laporan', compact(
            'tugas', 'divisiList', 'totalTugas', 'tugasSelesai', 'tugasBerjalan', 'tugasBelum'
        ));
    }
}
