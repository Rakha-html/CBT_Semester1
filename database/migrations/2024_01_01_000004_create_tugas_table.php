<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master tugas & target yang diberikan kepada panitia.
     */
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divisi_id')->constrained('divisi')->cascadeOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ditugaskan_ke')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul_tugas', 200);
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_target', ['numerik', 'dokumen', 'checklist'])->default('numerik');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');
            $table->enum('status', ['belum_dikerjakan', 'sedang_dikerjakan', 'selesai'])->default('belum_dikerjakan');
            $table->integer('progres_persen')->default(0);
            $table->integer('target_jumlah')->nullable();
            $table->integer('jumlah_tercapai')->default(0);
            $table->timestamp('tenggat_waktu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
