<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessoes_ativas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('dispositivo')->nullable(); // desktop, mobile, tablet
            $table->string('navegador')->nullable();
            $table->string('sistema_operacional')->nullable();
            $table->string('localizacao')->nullable();
            $table->timestamp('ultimo_acesso')->nullable();
            $table->timestamp('expira_em')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessoes_ativas');
    }
};
