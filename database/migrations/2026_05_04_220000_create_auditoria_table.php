<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('acao', 50); // criar, atualizar, excluir, login, logout, erro, visualizar
            $table->string('entidade', 50); // cliente, fornecedor, etc
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('metodo', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->text('observacao')->nullable();
            $table->timestamps();
            
            // Indices para performance
            $table->index(['acao']);
            $table->index(['entidade', 'entidade_id']);
            $table->index(['user_id']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
