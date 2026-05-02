<?php

if (! function_exists('auditoria')) {
    /**
     * Registra uma ação nos logs de auditoria.
     */
    function auditoria(
        string $acao,
        string $modulo,
        string $entidadeTipo,
        ?int   $entidadeId,
        ?array $dadosAnteriores,
        ?array $dadosNovos,
        ?string $descricao = null
    ): void {
        if (! config('app.audit_enabled', true)) {
            return;
        }
        try {
            \DB::table('logs_auditoria')->insert([
                'user_id'          => auth()->id(),
                'acao'             => $acao,
                'modulo'           => $modulo,
                'entidade_tipo'    => $entidadeTipo,
                'entidade_id'      => $entidadeId,
                'dados_anteriores' => $dadosAnteriores ? json_encode($dadosAnteriores, JSON_UNESCAPED_UNICODE) : null,
                'dados_novos'      => $dadosNovos      ? json_encode($dadosNovos,      JSON_UNESCAPED_UNICODE) : null,
                'ip'               => request()->ip(),
                'user_agent'       => request()->userAgent(),
                'descricao'        => $descricao,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } catch (\Throwable) {
            // Nunca deixar a auditoria quebrar o fluxo principal
        }
    }
}
