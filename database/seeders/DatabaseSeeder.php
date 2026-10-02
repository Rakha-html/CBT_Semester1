<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\PengumpulanTugas;
use App\Models\RiwayatAktivitas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial SPMB data.
     */
    public function run(): void
    {
        // ============================================================
        // 1. Master Divisi (5 Divisi)
        // ============================================================
        $divisiData = [
            ['nama_divisi' => 'Sosialisasi & Publikasi', 'deskripsi' => 'Bertanggung jawab atas promosi, media sosial, dan penyebaran informasi SPMB kepada calon peserta didik.'],
            ['nama_divisi' => 'Pendaftaran & Berkas', 'deskripsi' => 'Mengelola proses penerimaan formulir, verifikasi dokumen, dan administrasi pendaftaran peserta baru.'],
            ['nama_divisi' => 'Seleksi & Ujian', 'deskripsi' => 'Menyusun materi ujian, mengatur jadwal seleksi, dan mengolah hasil ujian seleksi SPMB.'],
            ['nama_divisi' => 'Keuangan & Administrasi', 'deskripsi' => 'Mengelola pemasukan biaya pendaftaran, pencatatan keuangan, dan pelaporan anggaran panitia.'],
            ['nama_divisi' => 'Logistik & Sarpras', 'deskripsi' => 'Menyediakan sarana prasarana, kebutuhan logistik acara, ruang ujian, dan peralatan pendukung SPMB.'],
        ];

        $divisiList = [];
        foreach ($divisiData as $d) {
            $divisiList[] = Divisi::create($d);
        }

        // ============================================================
        // 2. Akun Ketua SPMB (Admin)
        // ============================================================
        $ketua = User::create([
            'divisi_id'     => null, // Ketua mengawasi semua divisi
            'nama_lengkap'  => 'Administrator SPMB Wikrama',
            'email'         => 'adminspmb@smkwikrama.sch.id',
            'password'      => Hash::make('adminspmb2026'),
            'nomor_telepon' => '081234567890',
            'peran'         => 'ketua_spmb',
        ]);

        // Ketua memantau semua divisi
        foreach ($divisiList as $divisi) {
            $ketua->divisiDipantau()->attach($divisi->id, ['ditetapkan_pada' => now()]);
        }

        // ============================================================
        // 3. Akun Panitia Pelaksana (Dummy Akun untuk Setiap Divisi)
        // ============================================================
        $panitiaData = [
            [
                'divisi_id'     => $divisiList[0]->id, // Sosialisasi & Publikasi
                'nama_lengkap'  => 'Fajar Hidayat, S.I.Kom.',
                'email'         => 'panitia.publikasi@smkwikrama.sch.id',
                'password'      => Hash::make('password123'),
                'nomor_telepon' => '081234567001',
                'peran'         => 'panitia',
            ],
            [
                'divisi_id'     => $divisiList[1]->id, // Pendaftaran & Berkas
                'nama_lengkap'  => 'Siti Nurhaliza, S.Pd.',
                'email'         => 'panitia.pendaftaran@smkwikrama.sch.id',
                'password'      => Hash::make('password123'),
                'nomor_telepon' => '081234567002',
                'peran'         => 'panitia',
            ],
            [
                'divisi_id'     => $divisiList[2]->id, // Seleksi & Ujian
                'nama_lengkap'  => 'Budi Santoso, S.Kom.',
                'email'         => 'panitia.seleksi@smkwikrama.sch.id',
                'password'      => Hash::make('password123'),
                'nomor_telepon' => '081234567003',
                'peran'         => 'panitia',
            ],
            [
                'divisi_id'     => $divisiList[3]->id, // Keuangan & Administrasi
                'nama_lengkap'  => 'Dewi Anggraini, S.E.',
                'email'         => 'panitia.keuangan@smkwikrama.sch.id',
                'password'      => Hash::make('password123'),
                'nomor_telepon' => '081234567004',
                'peran'         => 'panitia',
            ],
            [
                'divisi_id'     => $divisiList[4]->id, // Logistik & Sarpras
                'nama_lengkap'  => 'Rizky Pratama, S.T.',
                'email'         => 'panitia.logistik@smkwikrama.sch.id',
                'password'      => Hash::make('password123'),
                'nomor_telepon' => '081234567005',
                'peran'         => 'panitia',
            ],
        ];

        $panitiaList = [];
        foreach ($panitiaData as $p) {
            $panitiaList[] = User::create($p);
        }

        // ============================================================
        // 4. Contoh Tugas
        // ============================================================
        $tugasData = [
            [
                'divisi_id'      => $divisiList[0]->id,
                'dibuat_oleh'    => $ketua->id,
                'ditugaskan_ke'  => $panitiaList[0]->id, // Fajar (Sosialisasi & Publikasi)
                'judul_tugas'    => 'Desain Brosur SPMB 2025/2026',
                'deskripsi'      => 'Buat desain brosur digital dan cetak untuk promosi SPMB tahun ajaran baru. Termasuk info jadwal, syarat, dan alur pendaftaran.',
                'tipe_target'    => 'dokumen',
                'prioritas'      => 'tinggi',
                'status'         => 'sedang_dikerjakan',
                'progres_persen' => 40,
                'target_jumlah'  => null,
                'jumlah_tercapai'=> 0,
                'tenggat_waktu'  => now()->addDays(14),
            ],
            [
                'divisi_id'      => $divisiList[1]->id,
                'dibuat_oleh'    => $ketua->id,
                'ditugaskan_ke'  => $panitiaList[1]->id, // Siti (Pendaftaran & Berkas)
                'judul_tugas'    => 'Verifikasi Berkas Pendaftar Gelombang 1',
                'deskripsi'      => 'Periksa kelengkapan dan keabsahan dokumen dari 150 calon peserta didik gelombang pertama.',
                'tipe_target'    => 'numerik',
                'prioritas'      => 'mendesak',
                'status'         => 'sedang_dikerjakan',
                'progres_persen' => 58,
                'target_jumlah'  => 150,
                'jumlah_tercapai'=> 87,
                'tenggat_waktu'  => now()->addDays(7),
            ],
            [
                'divisi_id'      => $divisiList[2]->id,
                'dibuat_oleh'    => $ketua->id,
                'ditugaskan_ke'  => $panitiaList[2]->id, // Budi (Seleksi & Ujian)
                'judul_tugas'    => 'Penyusunan Soal Ujian Seleksi',
                'deskripsi'      => 'Siapkan 100 soal pilihan ganda dan 10 soal esai untuk ujian seleksi masuk. Koordinasi dengan tim akademik.',
                'tipe_target'    => 'numerik',
                'prioritas'      => 'tinggi',
                'status'         => 'belum_dikerjakan',
                'progres_persen' => 0,
                'target_jumlah'  => 110,
                'jumlah_tercapai'=> 0,
                'tenggat_waktu'  => now()->addDays(21),
            ],
            [
                'divisi_id'      => $divisiList[3]->id,
                'dibuat_oleh'    => $ketua->id,
                'ditugaskan_ke'  => $panitiaList[3]->id, // Dewi (Keuangan & Administrasi)
                'judul_tugas'    => 'Rekap Pembayaran Biaya Formulir',
                'deskripsi'      => 'Buat rekap penerimaan pembayaran biaya formulir pendaftaran dari seluruh pendaftar yang sudah membayar.',
                'tipe_target'    => 'dokumen',
                'prioritas'      => 'sedang',
                'status'         => 'sedang_dikerjakan',
                'progres_persen' => 70,
                'target_jumlah'  => null,
                'jumlah_tercapai'=> 0,
                'tenggat_waktu'  => now()->addDays(10),
            ],
            [
                'divisi_id'      => $divisiList[4]->id,
                'dibuat_oleh'    => $ketua->id,
                'ditugaskan_ke'  => $panitiaList[4]->id, // Rizky (Logistik & Sarpras)
                'judul_tugas'    => 'Checklist Kesiapan Ruang Ujian',
                'deskripsi'      => 'Pastikan semua ruang ujian siap: meja, kursi, papan tulis, AC, proyektor, dan koneksi internet.',
                'tipe_target'    => 'checklist',
                'prioritas'      => 'tinggi',
                'status'         => 'belum_dikerjakan',
                'progres_persen' => 0,
                'target_jumlah'  => null,
                'jumlah_tercapai'=> 0,
                'tenggat_waktu'  => now()->addDays(5),
            ],
            [
                'divisi_id'      => $divisiList[0]->id,
                'dibuat_oleh'    => $ketua->id,
                'ditugaskan_ke'  => $panitiaList[0]->id, // Fajar (Sosialisasi & Publikasi)
                'judul_tugas'    => 'Upload Konten Media Sosial',
                'deskripsi'      => 'Posting 20 konten promosi ke Instagram, Facebook, dan website sekolah sesuai jadwal konten yang direncanakan.',
                'tipe_target'    => 'numerik',
                'prioritas'      => 'sedang',
                'status'         => 'sedang_dikerjakan',
                'progres_persen' => 60,
                'target_jumlah'  => 20,
                'jumlah_tercapai'=> 12,
                'tenggat_waktu'  => now()->addDays(30),
            ],
        ];

        $tugasList = [];
        foreach ($tugasData as $t) {
            $tugasList[] = Tugas::create($t);
        }

        // ============================================================
        // 5. Contoh Pengumpulan Tugas
        // ============================================================
        $pengumpulanData = [
            [
                'tugas_id'           => $tugasList[1]->id, // Verifikasi Berkas
                'dikirim_oleh'       => $panitiaList[1]->id, // Siti
                'jumlah_progres'     => 50,
                'tautan_berkas'      => null,
                'catatan_kendala'    => 'Beberapa berkas dari pendaftar luar kota masih belum lengkap. Sudah dikonfirmasi via telepon.',
                'status_verifikasi'  => 'disetujui',
                'catatan_ketua'      => 'Bagus, lanjutkan verifikasi. Prioritaskan yang sudah lengkap.',
                'dikirim_pada'       => now()->subDays(3),
            ],
            [
                'tugas_id'           => $tugasList[1]->id, // Verifikasi Berkas (batch 2)
                'dikirim_oleh'       => $panitiaList[1]->id, // Siti
                'jumlah_progres'     => 37,
                'tautan_berkas'      => null,
                'catatan_kendala'    => null,
                'status_verifikasi'  => 'menunggu',
                'catatan_ketua'      => null,
                'dikirim_pada'       => now()->subDay(),
            ],
            [
                'tugas_id'           => $tugasList[3]->id, // Rekap Pembayaran
                'dikirim_oleh'       => $panitiaList[3]->id, // Dewi
                'jumlah_progres'     => 0,
                'tautan_berkas'      => 'https://docs.google.com/spreadsheets/d/example-rekap-keuangan',
                'catatan_kendala'    => 'Ada 5 transaksi yang belum terkonfirmasi pembayarannya oleh bank.',
                'status_verifikasi'  => 'perlu_revisi',
                'catatan_ketua'      => 'Tolong tambahkan kolom tanggal konfirmasi bank dan status clearing. Sertakan juga bukti transfer yang belum terkonfirmasi.',
                'dikirim_pada'       => now()->subDays(2),
            ],
            [
                'tugas_id'           => $tugasList[5]->id, // Upload Konten Medsos
                'dikirim_oleh'       => $panitiaList[0]->id, // Fajar
                'jumlah_progres'     => 12,
                'tautan_berkas'      => 'https://drive.google.com/drive/folders/example-konten-medsos',
                'catatan_kendala'    => null,
                'status_verifikasi'  => 'disetujui',
                'catatan_ketua'      => 'Konten sudah sesuai panduan branding. Lanjutkan posting sesuai jadwal.',
                'dikirim_pada'       => now()->subDays(5),
            ],
        ];

        foreach ($pengumpulanData as $p) {
            PengumpulanTugas::create($p);
        }

        // ============================================================
        // 6. Contoh Riwayat Aktivitas
        // ============================================================
        $riwayatData = [
            [
                'tugas_id'         => $tugasList[0]->id,
                'admin_id'         => $ketua->id,
                'jenis_aktivitas'  => 'tugas_dibuat',
                'keterangan'       => 'Ketua SPMB membuat tugas "Desain Brosur SPMB 2025/2026".',
                'created_at'       => now()->subDays(10),
            ],
            [
                'tugas_id'         => $tugasList[1]->id,
                'admin_id'         => $ketua->id,
                'jenis_aktivitas'  => 'tugas_dibuat',
                'keterangan'       => 'Ketua SPMB membuat tugas "Verifikasi Berkas Pendaftar Gelombang 1".',
                'created_at'       => now()->subDays(8),
            ],
            [
                'tugas_id'         => $tugasList[1]->id,
                'admin_id'         => $panitiaList[1]->id, // Siti
                'jenis_aktivitas'  => 'progres_dikirim',
                'keterangan'       => 'Siti Nurhaliza mengirim laporan progres verifikasi berkas (50 dokumen).',
                'created_at'       => now()->subDays(3),
            ],
            [
                'tugas_id'         => $tugasList[1]->id,
                'admin_id'         => $ketua->id,
                'jenis_aktivitas'  => 'verifikasi_disetujui',
                'keterangan'       => 'Ketua SPMB menyetujui laporan progres verifikasi berkas batch 1.',
                'created_at'       => now()->subDays(3),
            ],
            [
                'tugas_id'         => $tugasList[3]->id,
                'admin_id'         => $ketua->id,
                'jenis_aktivitas'  => 'verifikasi_revisi',
                'keterangan'       => 'Ketua SPMB meminta revisi rekap pembayaran — format tabel belum sesuai.',
                'created_at'       => now()->subDays(2),
            ],
        ];

        foreach ($riwayatData as $r) {
            RiwayatAktivitas::create($r);
        }
    }
}
