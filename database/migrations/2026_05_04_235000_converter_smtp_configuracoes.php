<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Converte configuracoes SMTP do formato antigo (smtp_*) para o novo (mail_*)
     */
    public function up(): void
    {
        $map = [
            'smtp_host'      => 'mail_host',
            'smtp_porta'     => 'mail_port',
            'smtp_usuario'   => 'mail_username',
            'smtp_senha'     => 'mail_password',
            'smtp_remetente' => 'mail_from_address',
        ];

        foreach ($map as $chaveAntiga => $chaveNova) {
            $valor = DB::table('configuracoes')->where('chave', $chaveAntiga)->value('valor');
            if ($valor !== null) {
                DB::table('configuracoes')->updateOrInsert(
                    ['chave' => $chaveNova],
                    [
                        'valor' => $valor,
                        'grupo' => 'smtp',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        // Adiciona driver e encryption se nao existirem
        DB::table('configuracoes')->updateOrInsert(
            ['chave' => 'mail_driver'],
            ['valor' => 'smtp', 'grupo' => 'smtp', 'updated_at' => now(), 'created_at' => now()]
        );

        DB::table('configuracoes')->updateOrInsert(
            ['chave' => 'mail_encryption'],
            ['valor' => 'tls', 'grupo' => 'smtp', 'updated_at' => now(), 'created_at' => now()]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nao reverte para nao perder dados
    }
};
