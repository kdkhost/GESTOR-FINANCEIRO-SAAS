<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recorrências (template para contas recorrentes)
        Schema::create('recorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('descricao');
            $table->enum('tipo', ['pagar', 'receber'])->default('pagar');
            $table->decimal('valor', 15, 2);
            $table->enum('frequencia', ['diaria', 'semanal', 'quinzenal', 'mensal', 'bimestral', 'trimestral', 'semestral', 'anual'])->default('mensal');
            $table->integer('dia_vencimento')->nullable(); // 1-31
            $table->date('data_inicio');
            $table->date('data_fim')->nullable(); // null = sem fim
            $table->integer('total_parcelas')->nullable(); // null = infinito
            $table->integer('parcela_atual')->default(0);
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->nullOnDelete();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
            $table->boolean('ativo')->default(true);
            $table->date('proxima_execucao')->nullable();
            $table->date('ultima_execucao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'ativo', 'proxima_execucao']);
        });

        // Contas a pagar
        Schema::create('contas_pagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recorrencia_id')->nullable()->constrained('recorrencias')->nullOnDelete();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->nullOnDelete();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
            $table->foreignId('forma_pagamento_id')->nullable()->constrained('formas_pagamento')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->decimal('valor_pago', 15, 2)->nullable();
            $table->decimal('juros', 15, 2)->default(0);
            $table->decimal('multa', 15, 2)->default(0);
            $table->decimal('desconto', 15, 2)->default(0);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['pendente', 'pago', 'vencido', 'cancelado', 'parcialmente_pago'])->default('pendente');
            $table->integer('numero_parcela')->nullable();
            $table->integer('total_parcelas')->nullable();
            $table->string('codigo_barras')->nullable();
            $table->string('numero_documento')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('recorrente')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'data_vencimento']);
            $table->index(['user_id', 'data_vencimento']);
        });

        // Contas a receber
        Schema::create('contas_receber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recorrencia_id')->nullable()->constrained('recorrencias')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('subcategoria_id')->nullable()->constrained('subcategorias')->nullOnDelete();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->foreignId('forma_pagamento_id')->nullable()->constrained('formas_pagamento')->nullOnDelete();
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->decimal('valor_recebido', 15, 2)->nullable();
            $table->decimal('juros', 15, 2)->default(0);
            $table->decimal('multa', 15, 2)->default(0);
            $table->decimal('desconto', 15, 2)->default(0);
            $table->date('data_vencimento');
            $table->date('data_recebimento')->nullable();
            $table->enum('status', ['pendente', 'recebido', 'vencido', 'cancelado', 'parcialmente_recebido'])->default('pendente');
            $table->integer('numero_parcela')->nullable();
            $table->integer('total_parcelas')->nullable();
            $table->string('numero_documento')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('recorrente')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'data_vencimento']);
            $table->index(['user_id', 'data_vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contas_receber');
        Schema::dropIfExists('contas_pagar');
        Schema::dropIfExists('recorrencias');
    }
};
