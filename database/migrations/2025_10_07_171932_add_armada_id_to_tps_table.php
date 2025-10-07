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
        Schema::table('t_p_s', function (Blueprint $table) {
            $table->foreignId('armada_id')->nullable()->constrained('armadas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_p_s', function (Blueprint $table) {
            $table->dropForeign(['armada_id']);
            $table->dropColumn('armada_id');
        });
    }
};
