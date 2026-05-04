<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacao_templates', function (Blueprint $table) {
            $table->id();
            $table->string('canal', 20);
            $table->string('chave', 120)->unique();
            $table->string('nome', 160);
            $table->string('assunto', 200)->nullable();
            $table->longText('conteudo');
            $table->json('variaveis')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacao_templates');
    }
};

