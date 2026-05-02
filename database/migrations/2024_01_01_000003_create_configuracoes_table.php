<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('grupo')->default('geral'); // geral, smtp, pwa, aparencia, seguranca
            $table->string('chave')->unique();
            $table->text('valor')->nullable();
            $table->string('tipo')->default('texto'); // texto, booleano, numero, json, arquivo
            $table->string('label')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('sensivel')->default(false); // requer senha do superadmin para alterar
            $table->boolean('visivel')->default(true);
            $table->timestamps();

            $table->index('grupo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes');
    }
};
