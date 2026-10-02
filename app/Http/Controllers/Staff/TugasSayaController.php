<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\RiwayatAktivitas;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasSayaController extends Controller
{
    /**
     * Dashboard tugas milik panitia yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();

        $tugas = Tugas::with(['divisi', 'pengumpulan' => function ($q) use ($user) {
            $q->where('dikirim_oleh', $user->id)->latest();
        }])
            ->where('ditugaskan_ke', $user->id)
            ->latest()
            ->get();

        return view('staff.tugas-saya', compact('tugas'));
    }

    /**
     * Form lapor progres & bukti untuk tugas tertentu.
     */
    public function lapor(Tugas $tugas)
    {
        $user = Auth::user();

        // Pastikan tugas ini ditugaskan ke user yang login
        if ($tugas->ditugaskan_ke !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $tugas->load(['divisi', 'pengumpulan' => function ($q) {
            $q->latest();
        }, 'pengumpulan.pengirim']);

        // Pengumpulan terakhir yang perlu revisi (jika ada)
        $pengumpulanRevisi = $tugas->pengumpulan
            ->where('status_verifikasi', 'perlu_revisi')
            ->where('dikirim_oleh', $user->id)
            ->first();

        return view('staff.lapor', compact('tugas', 'pengumpulanRevisi'));
    }

    /**
     * Update kategori status dan persentase progres tugas oleh panitia.
     */
    public function updateStatus(Request $request, Tugas $tugas)
    {
        $user = Auth::user();

        // Pastikan tugas ini ditugaskan ke user atau divisi user
        if ($tugas->ditugaskan_ke !== $user->id && $tugas->divisi_id !== $user->divisi_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah tugas ini.');
        }

        $validated = $request->validate([
            'status'         => 'required|in:belum_dikerjakan,sedang_dikerjakan,selesai,sudah_dikerjakan',
            'progres_persen' => 'required|integer|min:0|max:100',
        ]);

        $status = $validated['status'];
        $progresPersen = (int) $validated['progres_persen'];

        // Aturan: Jika user mengetik progres = 100%, otomatis kategori menjadi "sudah dikerjakan" (selesai)
        if ($progresPersen >= 100) {
            $status = 'selesai';
            $progresPersen = 100;
        } elseif ($status === 'selesai' || $status === 'sudah_dikerjakan') {
            $status = 'selesai';
            $progresPersen = 100;
        } elseif ($progresPersen === 0) {
            if ($status === 'selesai' || $status === 'sudah_dikerjakan') {
                $status = 'belum_dikerjakan';
            }
        } elseif ($progresPersen > 0 && $progresPersen < 100) {
            $status = 'sedang_dikerjakan';
        }

        $updateData = [
            'status'         => $status,
            'progres_persen' => $progresPersen,
        ];

        // Sinkronisasi jumlah_tercapai jika tipe_target numerik
        if ($tugas->tipe_target === 'numerik' && $tugas->target_jumlah > 0) {
            $updateData['jumlah_tercapai'] = (int) round(($progresPersen / 100) * $tugas->target_jumlah);
        }

        $tugas->update($updateData);

        $kategoriLabel = match ($status) {
            'belum_dikerjakan'  => 'Belum Dikerjakan',
            'sedang_dikerjakan' => 'Sedang Dikerjakan',
            'selesai', 'sudah_dikerjakan' => 'Sudah Dikerjakan',
            default             => $status,
        };

        // Catat riwayat aktivitas
        RiwayatAktivitas::create([
            'tugas_id'        => $tugas->id,
            'admin_id'        => $user->id,
            'jenis_aktivitas' => 'progres_dikirim',
            'keterangan'      => "{$user->nama_lengkap} memperbarui status tugas menjadi \"{$kategoriLabel}\" dengan progres {$progresPersen}%.",
            'created_at'      => now(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'message'        => "Status berhasil diperbarui ke {$kategoriLabel} ({$progresPersen}%).",
                'status'         => $status,
                'status_label'   => $kategoriLabel,
                'progres_persen' => $progresPersen,
            ]);
        }

        return redirect()->route('staff.tugas-saya')
            ->with('success', "Status dan progres tugas berhasil diperbarui menjadi {$kategoriLabel} ({$progresPersen}%).");
    }

    /**
     * Simpan pengumpulan progres.
     */
    public function submitLaporan(Request $request, Tugas $tugas)
    {
        $user = Auth::user();

        if ($tugas->ditugaskan_ke !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $rules = [
            'catatan_kendala' => 'nullable|string|max:1000',
            'status'          => 'nullable|in:belum_dikerjakan,sedang_dikerjakan,selesai,sudah_dikerjakan',
            'progres_persen'  => 'nullable|integer|min:0|max:100',
        ];

        // Validasi adaptif sesuai tipe_target
        if ($tugas->tipe_target === 'numerik') {
            $rules['jumlah_progres'] = 'required|integer|min:1';
        } elseif ($tugas->tipe_target === 'dokumen') {
            $rules['tautan_berkas'] = 'required|string|max:255';
        } elseif ($tugas->tipe_target === 'checklist') {
            $rules['tautan_berkas'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        $pengumpulan = PengumpulanTugas::create([
            'tugas_id'          => $tugas->id,
            'dikirim_oleh'      => $user->id,
            'jumlah_progres'    => $validated['jumlah_progres'] ?? 0,
            'tautan_berkas'     => $validated['tautan_berkas'] ?? null,
            'catatan_kendala'   => $validated['catatan_kendala'] ?? null,
            'status_verifikasi' => 'menunggu',
            'dikirim_pada'      => now(),
        ]);

        // Hitung progres baru jika diberikan
        $newStatus = $request->input('status', $tugas->status);
        $newPersen = $request->has('progres_persen') ? (int) $request->input('progres_persen') : $tugas->progres_persen;

        if ($request->filled('progres_persen')) {
            if ($newPersen >= 100) {
                $newStatus = 'selesai';
                $newPersen = 100;
            } elseif ($newPersen > 0) {
                $newStatus = 'sedang_dikerjakan';
            }
        } elseif ($tugas->tipe_target === 'numerik' && $tugas->target_jumlah > 0 && isset($validated['jumlah_progres'])) {
            $estimasi = $tugas->jumlah_tercapai + (int) $validated['jumlah_progres'];
            $newPersen = min(100, (int) round(($estimasi / $tugas->target_jumlah) * 100));
            $newStatus = $newPersen >= 100 ? 'selesai' : 'sedang_dikerjakan';
        } else {
            if ($newStatus === 'belum_dikerjakan') {
                $newStatus = 'sedang_dikerjakan';
            }
        }

        $tugas->update([
            'status'         => $newStatus,
            'progres_persen' => $newPersen,
        ]);

        // Catat riwayat
        RiwayatAktivitas::create([
            'tugas_id'        => $tugas->id,
            'admin_id'        => $user->id,
            'jenis_aktivitas' => 'progres_dikirim',
            'keterangan'      => $user->nama_lengkap . ' mengirim laporan progres untuk tugas "' . $tugas->judul_tugas . '" (' . $newPersen . '%).',
            'created_at'      => now(),
        ]);

        return redirect()->route('staff.tugas-saya')
            ->with('success', 'Laporan progres berhasil dikirim dan menunggu verifikasi.');
    }
}
