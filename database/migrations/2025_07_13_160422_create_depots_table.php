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
        Schema::create('depots', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            // $table->string('kode')->unique();
            $table->string('alamat');
            // $table->decimal('latitude', 10, 7);
            // $table->decimal('longitude', 10, 7);
            $table->integer('kapasitas');
            $table->enum('status', ['aktif', 'maintenance', 'nonaktif']);
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depots');
    }
};
