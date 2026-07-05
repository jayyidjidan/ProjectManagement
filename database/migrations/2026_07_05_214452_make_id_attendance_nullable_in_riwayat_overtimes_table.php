<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('riwayat_overtimes', function (Blueprint $table) {
            // 1. Hapus Foreign Key lama (Beri tanda komentar sementara jika foreign key sudah terlanjur terhapus dari percobaan sebelumnya)
            // $table->dropForeign('riwayat_overtimes_id_attendance_foreign');

            // 2. GANTI MENJADI unsignedInteger (bukan unsignedBigInteger)
            $table->unsignedInteger('id_attendance')->nullable()->change();

            // 3. Buat kembali Foreign Key-nya
            $table->foreign('id_attendance')
                  ->references('id_attendance')
                  ->on('attendances')
                  ->onDelete('set null'); 
        });
    }
    
    public function down(): void
    {
        Schema::table('riwayat_overtimes', function (Blueprint $table) {
            // 1. Hapus Foreign Key
            $table->dropForeign('riwayat_overtimes_id_attendance_foreign');

            // 2. Kembalikan kolom menjadi TIDAK nullable
            $table->unsignedBigInteger('id_attendance')->nullable(false)->change();

            // 3. Buat kembali Foreign Key seperti semula
            $table->foreign('id_attendance')
                  ->references('id_attendance')
                  ->on('attendances')
                  ->onDelete('cascade'); // Sesuaikan dengan behavior asal Anda
        });
    }
};