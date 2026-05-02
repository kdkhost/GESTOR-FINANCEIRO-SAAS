<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metas_financeiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->decimal('valor_alvo', 15, 2);
            $table->decimal('valor_atual', 15, 2)->default(0);
            $table->date('data_inicio');
            $table->date('data_prazo');
            $table->foreignId('conta_bancaria_id')->nullable()->constrained('contas_bancarias')->nullOnDelete();
            $table->enum('status', ['ativa', 'concluida', 'cancelada', 'pausada'])->default('ativa');
            $table->string('icone')->nullable();
            $table->string('cor', 7)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });

        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('titulo');
            $table->decimal('valor_limite', 15, 2);
            $table->decimal('valor_gasto', 15, 2)->default(0);
            $table->integer('mes');   // 1-12
            $table->integer('ano');
            $table->enum('alerta_percentual', ['50', '75', '90', '100'])->default('75');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'categoria_id', 'mes', 'ano']);
            $table->index(['user_id', 'mes', 'ano']);
        });

        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('entidade_tipo'); // contas_pagar, contas_receber, etc.
            $table->unsignedBigInteger('entidade_id');
            $table->string('nome_original');
            $table->string('nome_armazenado');
            $table->string('caminho');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamanho_bytes');
            $table->timestamps();

            $table->index(['entidade_tipo', 'entidade_id']);
        });

        Schema::create('logs_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('acao'); // criou, editou, excluiu, acessou, etc.
            $table->string('modulo')->nullable();
            $table->string('entidade_tipo')->nullable();
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['entidade_tipo', 'entidade_id']);
            $table->index('modulo');
        });

        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo'); // vencimento, pagamento, meta, orcamento, sistema
            $table->string('titulo');
            $table->text('mensagem');
            $table->json('dados')->nullable();
            $table->string('url')->nullable();
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'lida', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
        Schema::dropIfExists('logs_auditoria');
        Schema::dropIfExists('anexos');
        Schema::dropIfExists('orcamentos');
        Schema::dropIfExists('metas_financeiras');
    }
};
