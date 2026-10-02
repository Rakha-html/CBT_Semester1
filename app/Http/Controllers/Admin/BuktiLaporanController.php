<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\PengumpulanTugas;
use App\Models\RiwayatAktivitas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuktiLaporanController extends Controller
{
    /**
     * Halaman bukti laporan tugas dari seluruh panitia divisi.
     */
    public function index(Request $request)
    {
        $query = PengumpulanTugas::with(['tugas.divisi', 'pengirim'])
            ->latest('dikirim_pada');

        // Filter berdasarkan status verifikasi
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Filter berdasarkan divisi
        if ($request->filled('divisi_id')) {
            $divisiId = $request->divisi_id;
            $query->whereHas('tugas', function ($q) use ($divisiId) {
                $q->where('divisi_id', $divisiId);
            });
        }

        // Pencarian nama panitia atau judul tugas
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('tugas', function ($qt) use ($search) {
                    $qt->where('judul_tugas', 'like', "%{$search}%");
                })->orWhereHas('pengirim', function ($qu) use ($search) {
                    $qu->where('nama_lengkap', 'like', "%{$search}%");
                })->orWhere('catatan_kendala', 'like', "%{$search}%");
            });
        }

        $pengumpulanList = $query->paginate(12)->withQueryString();

        // Statistik laporan
        $totalLaporan = PengumpulanTugas::count();
        $menungguVerifikasi = PengumpulanTugas::where('status_verifikasi', 'menunggu')->count();
        $disetujuiCount = PengumpulanTugas::where('status_verifikasi', 'disetujui')->count();
        $perluRevisiCount = PengumpulanTugas::where('status_verifikasi', 'perlu_revisi')->count();

        $divisiList = Divisi::orderBy('nama_divisi')->get();

        return view('admin.bukti-laporan', compact(
            'pengumpulanList',
            'divisiList',
            'totalLaporan',
            'menungguVerifikasi',
            'disetujuiCount',
            'perluRevisiCount'
        ));
    }

    /**
     * Verifikasi laporan tugas (Setujui / Minta Revisi).
     */
    public function verifikasi(Request $request, PengumpulanTugas $pengumpulan)
    {
        $request->validate([
            'aksi'          => 'required|in:disetujui,perlu_revisi',
            'catatan_ketua' => 'nullable|string|max:1000',
        ]);

        $tugas = $pengumpulan->tugas;
        $aksi = $request->aksi;

        if ($aksi === 'disetujui') {
            $pengumpulan->update([
                'status_verifikasi' => 'disetujui',
                'catatan_ketua'     => $request->catatan_ketua,
            ]);

            // Akumulasi progres numerik
            if ($tugas->tipe_target === 'numerik' && $pengumpulan->jumlah_progres > 0) {
                $totalCapaian = PengumpulanTugas::where('tugas_id', $tugas->id)
                    ->where('status_verifikasi', 'disetujui')
                    ->sum('jumlah_progres');

                $tugas->jumlah_tercapai = $totalCapaian;
            }

            // Update status & progres persen
            if ($tugas->tipe_target === 'numerik' && $tugas->target_jumlah > 0) {
                if ($tugas->jumlah_tercapai >= $tugas->target_jumlah) {
                    $tugas->status = 'selesai';
                    $tugas->progres_persen = 100;
                } else {
                    $tugas->progres_persen = min(100, (int) round(($tugas->jumlah_tercapai / $tugas->target_jumlah) * 100));
                    if ($tugas->status === 'belum_dikerjakan') {
                        $tugas->status = 'sedang_dikerjakan';
                    }
                }
            } else {
                // Untuk dokumen / checklist, persetujuan menandakan tugas selesai
                $tugas->status = 'selesai';
                $tugas->progres_persen = 100;
            }

            $tugas->save();

            RiwayatAktivitas::create([
                'tugas_id'        => $tugas->id,
                'admin_id'        => Auth::id(),
                'jenis_aktivitas' => 'verifikasi_disetujui',
                'keterangan'      => 'Ketua SPMB menyetujui bukti laporan dari ' . $pengumpulan->pengirim->nama_lengkap . '.',
                'created_at'      => now(),
            ]);

            return redirect()->back()->with('success', 'Bukti laporan berhasil disetujui.');

        } elseif ($aksi === 'perlu_revisi') {
            $pengumpulan->update([
                'status_verifikasi' => 'perlu_revisi',
                'catatan_ketua'     => $request->catatan_ketua,
            ]);

            RiwayatAktivitas::create([
                'tugas_id'        => $tugas->id,
                'admin_id'        => Auth::id(),
                'jenis_aktivitas' => 'verifikasi_revisi',
                'keterangan'      => 'Ketua SPMB meminta revisi laporan dari ' . $pengumpulan->pengirim->nama_lengkap . '.',
                'created_at'      => now(),
            ]);

            return redirect()->back()->with('success', 'Permintaan revisi berhasil dikirim ke panitia.');
        }

        return redirect()->back();
    }
}
