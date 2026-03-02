<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localisation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_seance');
            $table->integer('id_boitier');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('altitude', 8, 2)->nullable();
            $table->dateTime('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['id_seance', 'recorded_at'], 'idx_localisation_seance_time');
            $table->index(['id_boitier', 'recorded_at'], 'idx_localisation_boitier_time');

            $table->foreign('id_seance')
                ->references('id')
                ->on('seance')
                ->onDelete('cascade');

            $table->foreign('id_boitier')
                ->references('id')
                ->on('boitier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localisation');
    }
};

