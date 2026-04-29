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
      Schema::create('penghuni', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('ktp_photo')->nullable();
        $table->enum('status', ['tetap', 'kontrak']);
        $table->string('phone');
        $table->boolean('status_pernikahan')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghuni');
    }
};
