<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rumah_id')->constrained('rumah')->cascadeOnDelete();
            $table->foreignId('penghuni_id')->constrained('penghuni')->cascadeOnDelete();
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('uang_security')->default(100000);
            $table->integer('uang_kebersihan')->default(15000);
            $table->integer('total');
            $table->enum('status', ['lunas', 'belum_bayar'])->default('belum_bayar');
            $table->enum('jenis_pembayaran', ['bulanan', 'tahunan'])->default('bulanan');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
