<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\RiwayatAktivitas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    /**
     * Form buat tugas baru.
     */
    public function create()
    {
        $divisiList = Divisi::orderBy('nama_divisi')->get();
        $panitiaList = User::where('peran', 'panitia')->orderBy('nama_lengkap')->get();

        return view('admin.tugas-create', compact('divisiList', 'panitiaList'));
    }

    /**
     * Simpan tugas baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'divisi_id'      => 'required|exists:divisi,id',
            'ditugaskan_ke'  => 'nullable|exists:users,id',
            'judul_tugas'    => 'required|string|max:200',
            'deskripsi'      => 'nullable|string',
            'tipe_target'    => 'required|in:numerik,dokumen,checklist',
            'prioritas'      => 'required|in:rendah,sedang,tinggi,mendesak',
            'target_jumlah'  => 'nullable|integer|min:1',
            'tenggat_waktu'  => 'nullable|date|after_or_equal:today',
        ]);

        $tugas = Tugas::create([
            ...$validated,
            'dibuat_oleh'     => Auth::id(),
            'status'          => 'belum_dikerjakan',
            'jumlah_tercapai' => 0,
        ]);

        // Catat riwayat aktivitas
        RiwayatAktivitas::create([
            'tugas_id'        => $tugas->id,
            'admin_id'        => Auth::id(),
            'jenis_aktivitas' => 'tugas_dibuat',
            'keterangan'      => 'Tugas "' . $tugas->judul_tugas . '" dibuat oleh ' . Auth::user()->nama_lengkap . '.',
            'created_at'      => now(),
        ]);

        return redirect()->route('admin.monitoring')
            ->with('success', 'Tugas berhasil dibuat.');
    }
}
