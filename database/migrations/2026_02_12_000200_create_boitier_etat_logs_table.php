<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("boitier_etat_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("id_boitier");
            $table->integer("id_utilisateur");
            $table->string("ancien_etat", 50)->nullable();
            $table->string("nouvel_etat", 50);
            $table->dateTime("created_at")->useCurrent();

            $table->index(["id_boitier", "created_at"], "idx_boitier_etat_logs_boitier_time");
            $table->index(["id_utilisateur", "created_at"], "idx_boitier_etat_logs_user_time");

            $table->foreign("id_boitier")
                ->references("id")
                ->on("boitier")
                ->onDelete("cascade");

            $table->foreign("id_utilisateur")
                ->references("id")
                ->on("utilisateur")
                ->onDelete("cascade");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("boitier_etat_logs");
    }
};

