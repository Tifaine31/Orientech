<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("app_logs", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("source", 40)->default("app");
            $table->string("level", 20)->default("info");
            $table->string("event_type", 80)->nullable();
            $table->text("message");
            $table->json("context")->nullable();
            $table->integer("id_utilisateur")->nullable();
            $table->string("http_method", 10)->nullable();
            $table->string("path", 255)->nullable();
            $table->smallInteger("status_code")->nullable();
            $table->string("ip_address", 45)->nullable();
            $table->dateTime("created_at")->useCurrent();

            $table->index(["created_at"], "idx_app_logs_created_at");
            $table->index(["source", "created_at"], "idx_app_logs_source_created_at");
            $table->index(["id_utilisateur", "created_at"], "idx_app_logs_user_created_at");
            $table->index(["event_type", "created_at"], "idx_app_logs_event_created_at");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("app_logs");
    }
};

