<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Receitas avulsas
        Schema::create('receitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conta_receber_id')->nullable()->constrained('contas_receber')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->nullOnDelete();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->foreignId('forma_pagamento_id')->nullable()->constrained('formas_pagamento')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->date('data_receita');
            $table->date('data_competencia')->nullable();
            $table->boolean('confirmado')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'data_receita']);
            $table->index(['user_id', 'categoria_id']);
        });

        // Despesas avulsas
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conta_pagar_id')->nullable()->constrained('contas_pagar')->nullOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->nullOnDelete();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
            $table->foreignId('forma_pagamento_id')->nullable()->constrained('formas_pagamento')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->date('data_despesa');
            $table->date('data_competencia')->nullable();
            $table->boolean('confirmado')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'data_despesa']);
            $table->index(['user_id', 'categoria_id']);
        });

        // Transferências entre contas
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conta_origem_id')->constrained('contas_bancarias')->cascadeOnDelete();
            $table->foreignId('conta_destino_id')->constrained('contas_bancarias')->cascadeOnDelete();
            $table->decimal('valor', 15, 2);
            $table->decimal('taxa', 15, 2)->default(0);
            $table->date('data_transferencia');
            $table->string('descricao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'data_transferencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferencias');
        Schema::dropIfExists('despesas');
        Schema::dropIfExists('receitas');
    }
};
