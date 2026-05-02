<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->enum('tipo', ['receita', 'despesa', 'ambos'])->default('despesa');
            $table->string('icone')->nullable();
            $table->string('cor', 7)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'tipo', 'ativo']);
        });

        Schema::create('subcategorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->string('nome');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['categoria_id', 'ativo']);
        });

        Schema::create('centros_custo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->string('codigo')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'ativo']);
        });

        Schema::create('formas_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->enum('tipo', ['dinheiro', 'pix', 'ted', 'doc', 'boleto', 'cartao_debito', 'cartao_credito', 'cheque', 'outro'])->default('outro');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formas_pagamento');
        Schema::dropIfExists('centros_custo');
        Schema::dropIfExists('subcategorias');
        Schema::dropIfExists('categorias');
    }
};
