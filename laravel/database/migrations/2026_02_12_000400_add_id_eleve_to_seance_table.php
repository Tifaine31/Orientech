<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("seance")) {
            return;
        }

        Schema::table("seance", function (Blueprint $table) {
            if (!Schema::hasColumn("seance", "id_eleve")) {
                $table->integer("id_eleve")->nullable()->after("id");
                $table->index("id_eleve", "idx_seance_id_eleve");
            }
        });

        // Backfill: take one student from scan history for existing sessions.
        DB::statement("
            UPDATE seance s
            JOIN (
                SELECT id_seance, MIN(id_eleve) AS id_eleve
                FROM scan_seance
                WHERE id_eleve IS NOT NULL
                GROUP BY id_seance
            ) x ON x.id_seance = s.id
            SET s.id_eleve = x.id_eleve
            WHERE s.id_eleve IS NULL
        ");

        // Add FK only if absent.
        $fkExists = DB::selectOne("
            SELECT COUNT(*) AS c
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'seance'
              AND COLUMN_NAME = 'id_eleve'
              AND REFERENCED_TABLE_NAME = 'utilisateur'
        ");
        if ((int)($fkExists->c ?? 0) === 0) {
            Schema::table("seance", function (Blueprint $table) {
                $table->foreign("id_eleve", "fk_seance_id_eleve")
                    ->references("id")
                    ->on("utilisateur")
                    ->onDelete("set null");
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable("seance") || !Schema::hasColumn("seance", "id_eleve")) {
            return;
        }

        Schema::table("seance", function (Blueprint $table) {
            try {
                $table->dropForeign("fk_seance_id_eleve");
            } catch (\Throwable $e) {
            }
            try {
                $table->dropIndex("idx_seance_id_eleve");
            } catch (\Throwable $e) {
            }
            $table->dropColumn("id_eleve");
        });
    }
};

