<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->string('comando'); // Ex: app:processar-recorrencias
            $table->string('expressao_cron')->default('0 * * * *'); // Formato cron padrão
            $table->boolean('ativo')->default(true);
            $table->boolean('executar_manualmente')->default(false);
            $table->timestamp('ultima_execucao')->nullable();
            $table->timestamp('proxima_execucao')->nullable();
            $table->enum('ultimo_status', ['sucesso', 'erro', 'executando', 'pendente'])->default('pendente');
            $table->integer('duracao_ms')->nullable();
            $table->timestamps();
        });

        Schema::create('cron_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cron_job_id')->constrained('cron_jobs')->cascadeOnDelete();
            $table->enum('status', ['sucesso', 'erro'])->default('sucesso');
            $table->text('saida')->nullable();
            $table->text('erro')->nullable();
            $table->integer('duracao_ms')->nullable();
            $table->timestamp('executado_em');
            $table->timestamps();

            $table->index(['cron_job_id', 'executado_em']);
        });

        Schema::create('gateways_pagamento', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('identificador')->unique(); // mercadopago, pagseguro, stripe
            $table->json('credenciais')->nullable(); // criptografadas
            $table->boolean('ativo')->default(false);
            $table->boolean('sandbox')->default(true);
            $table->json('configuracoes')->nullable();
            $table->timestamps();
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome');
            $table->string('url');
            $table->json('eventos'); // array de eventos que disparam
            $table->string('secret')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('tentativas_max')->default(3);
            $table->timestamp('ultimo_disparo')->nullable();
            $table->enum('ultimo_status', ['sucesso', 'erro', 'pendente'])->default('pendente');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('snapshots_saude_financeira', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('indice'); // 0-100
            $table->string('classificacao'); // Excelente, Boa, Atenção, Crítica, Emergencial
            $table->json('fatores'); // breakdown dos fatores do cálculo
            $table->json('recomendacoes')->nullable();
            $table->integer('mes');
            $table->integer('ano');
            $table->timestamps();

            $table->unique(['user_id', 'mes', 'ano']);
            $table->index(['user_id', 'ano', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snapshots_saude_financeira');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('gateways_pagamento');
        Schema::dropIfExists('cron_logs');
        Schema::dropIfExists('cron_jobs');
    }
};
