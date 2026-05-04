<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cep', 10)->nullable()->after('telefone');
            $table->string('logradouro', 150)->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('logradouro');
            $table->string('complemento', 120)->nullable()->after('numero');
            $table->string('bairro', 120)->nullable()->after('complemento');
            $table->string('cidade', 120)->nullable()->after('bairro');
            $table->string('estado', 2)->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado']);
        });
    }
};

