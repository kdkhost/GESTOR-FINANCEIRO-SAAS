<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Usuário já existe no Laravel padrão, apenas estendemos
            $table->string('cpf', 14)->nullable()->unique()->after('email');
            $table->string('telefone', 20)->nullable()->after('cpf');
            $table->string('avatar')->nullable()->after('telefone');
            $table->enum('status', ['ativo', 'inativo', 'bloqueado'])->default('ativo')->after('avatar');
            $table->enum('tipo', ['superadmin', 'admin', 'usuario'])->default('usuario')->after('status');
            $table->boolean('dois_fatores')->default(false)->after('tipo');
            $table->string('dois_fatores_secret')->nullable()->after('dois_fatores');
            $table->integer('tentativas_login')->default(0)->after('dois_fatores_secret');
            $table->timestamp('bloqueado_ate')->nullable()->after('tentativas_login');
            $table->string('ultimo_ip', 45)->nullable()->after('bloqueado_ate');
            $table->string('ultimo_user_agent')->nullable()->after('ultimo_ip');
            $table->timestamp('ultimo_acesso_em')->nullable()->after('ultimo_user_agent');
            $table->string('timezone')->default('America/Sao_Paulo')->after('ultimo_acesso_em');
            $table->string('locale')->default('pt_BR')->after('timezone');
            $table->json('preferencias')->nullable()->after('locale');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cpf','telefone','avatar','status','tipo','dois_fatores',
                'dois_fatores_secret','tentativas_login','bloqueado_ate',
                'ultimo_ip','ultimo_user_agent','ultimo_acesso_em',
                'timezone','locale','preferencias','deleted_at',
            ]);
        });
    }
};
