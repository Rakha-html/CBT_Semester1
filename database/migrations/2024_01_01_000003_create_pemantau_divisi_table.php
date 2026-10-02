<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pivot many-to-many antara users (admin/panitia) dan divisi
     * Digunakan untuk menentukan divisi mana saja yang dipantau oleh seorang user.
     */
    public function up(): void
    {
        Schema::create('pemantau_divisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('divisi_id')->constrained('divisi')->cascadeOnDelete();
            $table->timestamp('ditetapkan_pada')->useCurrent();

            $table->unique(['admin_id', 'divisi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemantau_divisi');
    }
};
