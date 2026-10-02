<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\PengumpulanTugas;
use App\Models\RiwayatAktivitas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Tabel pantau capaian tugas.
     * Mendukung filter divisi, status, dan pencarian keyword.
     */
    public function index(Request $request)
    {
        $query = Tugas::with(['divisi', 'penanggungjawab', 'pembuat']);

        // Filter berdasarkan divisi
        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            if ($request->status === 'selesai' || $request->status === 'sudah_dikerjakan') {
                $query->whereIn('status', ['selesai', 'sudah_dikerjakan']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Pencarian keyword pada judul_tugas atau deskripsi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_tugas', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $tugas = $query->latest()->paginate(15)->withQueryString();
        $divisiList = Divisi::orderBy('nama_divisi')->get();

        return view('admin.monitoring', compact('tugas', 'divisiList'));
    }

    /**
     * Menampilkan detail tugas beserta semua pengumpulan/submission.
     */
    public function review(Tugas $tugas)
    {
        $tugas->load(['divisi', 'penanggungjawab', 'pembuat', 'pengumpulan.pengirim', 'riwayatAktivitas.user']);

        return view('admin.review', compact('tugas'));
    }

    /**
     * Proses verifikasi pengumpulan tugas:
     * - disetujui: akumulasi jumlah_tercapai, update status tugas jika target terpenuhi
     * - perlu_revisi: simpan catatan_ketua
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

            // Akumulasi jumlah_tercapai pada tugas
            if ($tugas->tipe_target === 'numerik' && $pengumpulan->jumlah_progres > 0) {
                $totalCapaian = PengumpulanTugas::where('tugas_id', $tugas->id)
                    ->where('status_verifikasi', 'disetujui')
                    ->sum('jumlah_progres');

                $tugas->jumlah_tercapai = $totalCapaian;
            }

            // Cek apakah target terpenuhi untuk auto-selesai
            if ($tugas->tipe_target === 'numerik' && $tugas->target_jumlah) {
                if ($tugas->jumlah_tercapai >= $tugas->target_jumlah) {
                    $tugas->status = 'selesai';
                    $tugas->progres_persen = 100;
                } else {
                    $tugas->progres_persen = min(100, (int) round(($tugas->jumlah_tercapai / $tugas->target_jumlah) * 100));
                }
            } elseif (in_array($tugas->tipe_target, ['dokumen', 'checklist'])) {
                // Untuk dokumen/checklist, disetujui = selesai
                $tugas->status = 'selesai';
                $tugas->progres_persen = 100;
            }

            if ($tugas->status !== 'selesai' && $tugas->status === 'belum_dikerjakan') {
                $tugas->status = 'sedang_dikerjakan';
            }

            $tugas->save();

            // Catat riwayat
            RiwayatAktivitas::create([
                'tugas_id'        => $tugas->id,
                'admin_id'        => Auth::id(),
                'jenis_aktivitas' => 'verifikasi_disetujui',
                'keterangan'      => 'Laporan progres dari ' . $pengumpulan->pengirim->nama_lengkap . ' disetujui.',
                'created_at'      => now(),
            ]);

        } elseif ($aksi === 'perlu_revisi') {
            $pengumpulan->update([
                'status_verifikasi' => 'perlu_revisi',
                'catatan_ketua'     => $request->catatan_ketua,
            ]);

            // Catat riwayat
            RiwayatAktivitas::create([
                'tugas_id'        => $tugas->id,
                'admin_id'        => Auth::id(),
                'jenis_aktivitas' => 'verifikasi_revisi',
                'keterangan'      => 'Ketua meminta revisi untuk laporan dari ' . $pengumpulan->pengirim->nama_lengkap . '.',
                'created_at'      => now(),
            ]);
        }

        return redirect()->route('admin.review', $tugas)
            ->with('success', $aksi === 'disetujui'
                ? 'Laporan berhasil disetujui.'
                : 'Revisi telah diminta.');
    }
}
