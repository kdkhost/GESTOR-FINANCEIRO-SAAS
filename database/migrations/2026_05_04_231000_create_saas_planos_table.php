<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 160);
            $table->string('slug', 120)->unique();
            $table->text('descricao')->nullable();
            $table->decimal('valor_mensal', 12, 2)->default(0);
            $table->decimal('valor_anual', 12, 2)->nullable();
            $table->json('limites')->nullable();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_planos');
    }
};

