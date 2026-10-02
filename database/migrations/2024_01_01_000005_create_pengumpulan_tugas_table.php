<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengumpulan tugas - laporan progres & validasi dari panitia.
     */
    public function up(): void
    {
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('dikirim_oleh')->constrained('users')->cascadeOnDelete();
            $table->integer('jumlah_progres')->default(0);
            $table->string('tautan_berkas', 255)->nullable();
            $table->text('catatan_kendala')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'perlu_revisi'])->default('menunggu');
            $table->text('catatan_ketua')->nullable();
            $table->timestamp('dikirim_pada')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
