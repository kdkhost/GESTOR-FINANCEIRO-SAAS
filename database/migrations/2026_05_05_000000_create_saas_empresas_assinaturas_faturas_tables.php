<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia', 160);
            $table->string('razao_social', 200)->nullable();
            $table->string('cnpj', 30)->nullable()->index();
            $table->string('email', 160)->nullable()->index();
            $table->string('telefone', 30)->nullable();

            $table->string('cep', 12)->nullable();
            $table->string('logradouro', 200)->nullable();
            $table->string('numero', 30)->nullable();
            $table->string('complemento', 120)->nullable();
            $table->string('bairro', 120)->nullable();
            $table->string('cidade', 120)->nullable();
            $table->string('estado', 20)->nullable();

            $table->string('status', 20)->default('ativo')->index();
            $table->string('timezone', 80)->nullable();
            $table->string('locale', 20)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('saas_assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('saas_empresas')->cascadeOnDelete();
            $table->foreignId('plano_id')->constrained('saas_planos')->restrictOnDelete();
            $table->string('status', 20)->default('ativa')->index();
            $table->timestamp('inicio_em')->nullable();
            $table->timestamp('fim_em')->nullable();
            $table->timestamp('proxima_cobranca_em')->nullable();
            $table->string('gateway', 40)->nullable()->index();
            $table->string('gateway_ref', 120)->nullable()->index();
            $table->timestamp('trial_ate')->nullable();
            $table->timestamp('cancelada_em')->nullable();
            $table->string('cancelamento_motivo', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('saas_faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('saas_empresas')->cascadeOnDelete();
            $table->foreignId('assinatura_id')->nullable()->constrained('saas_assinaturas')->nullOnDelete();
            $table->string('status', 20)->default('aberta')->index();
            $table->string('competencia', 10)->index();
            $table->decimal('valor', 12, 2)->default(0);
            $table->timestamp('vencimento_em')->nullable()->index();
            $table->timestamp('pago_em')->nullable()->index();
            $table->string('gateway', 40)->nullable()->index();
            $table->string('gateway_ref', 120)->nullable()->index();
            $table->string('link_pagamento', 1000)->nullable();
            $table->longText('pix_copia_e_cola')->nullable();
            $table->longText('boleto_linha_digitavel')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_faturas');
        Schema::dropIfExists('saas_assinaturas');
        Schema::dropIfExists('saas_empresas');
    }
};

