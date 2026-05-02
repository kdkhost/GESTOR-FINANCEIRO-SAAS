<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('codigo', 10)->nullable();
            $table->string('nome');
            $table->string('logo')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'ativo']);
        });

        Schema::create('contas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('banco_id')->nullable()->constrained('bancos')->nullOnDelete();
            $table->string('nome');
            $table->string('agencia', 20)->nullable();
            $table->string('numero_conta', 30)->nullable();
            $table->string('digito', 5)->nullable();
            $table->enum('tipo', ['corrente', 'poupanca', 'salario', 'investimento', 'carteira', 'outro'])->default('corrente');
            $table->decimal('saldo_inicial', 15, 2)->default(0);
            $table->decimal('saldo_atual', 15, 2)->default(0);
            $table->boolean('incluir_no_total')->default(true);
            $table->boolean('ativo')->default(true);
            $table->string('cor', 7)->nullable(); // hex color
            $table->string('icone')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'ativo']);
        });

        Schema::create('cartoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->string('nome');
            $table->enum('bandeira', ['visa', 'mastercard', 'elo', 'amex', 'hipercard', 'outro'])->default('outro');
            $table->decimal('limite', 15, 2)->default(0);
            $table->decimal('limite_disponivel', 15, 2)->default(0);
            $table->integer('dia_vencimento')->nullable(); // 1-31
            $table->integer('dia_fechamento')->nullable(); // 1-31
            $table->string('ultimos_digitos', 4)->nullable();
            $table->boolean('ativo')->default(true);
            $table->string('cor', 7)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartoes');
        Schema::dropIfExists('contas_bancarias');
        Schema::dropIfExists('bancos');
    }
};
