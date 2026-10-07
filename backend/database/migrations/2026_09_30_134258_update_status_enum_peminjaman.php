<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('diajukan','dipinjam','menunggu_pengembalian','dikembalikan','selesai','telat') NOT NULL DEFAULT 'diajukan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('diajukan','dipinjam','dikembalikan','telat') NOT NULL DEFAULT 'diajukan'");
    }
};