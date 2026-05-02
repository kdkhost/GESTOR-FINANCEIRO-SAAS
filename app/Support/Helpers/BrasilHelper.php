<?php

/**
 * Helpers globais de formatação brasileira.
 * Carregado automaticamente via composer.json autoload.files
 */

if (! function_exists('moeda_br')) {
    /**
     * Formata um valor para moeda brasileira.
     *
     * @param float|string|null $valor
     * @param bool $simbolo Inclui símbolo R$
     * @return string
     */
    function moeda_br(float|string|null $valor, bool $simbolo = true): string
    {
        $valor = (float) ($valor ?? 0);
        $formatado = number_format($valor, 2, ',', '.');
        return $simbolo ? "R$ {$formatado}" : $formatado;
    }
}

if (! function_exists('moeda_para_float')) {
    /**
     * Converte string de moeda brasileira para float.
     *
     * @param string|null $valor Ex: "1.234,56" ou "R$ 1.234,56"
     * @return float
     */
    function moeda_para_float(string|null $valor): float
    {
        if (empty($valor)) {
            return 0.0;
        }
        $valor = preg_replace('/[^\d,]/', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return (float) $valor;
    }
}

if (! function_exists('formatar_cpf')) {
    /**
     * Formata CPF: 000.000.000-00
     */
    function formatar_cpf(string|null $cpf): string
    {
        if (empty($cpf)) {
            return '';
        }
        $cpf = preg_replace('/\D/', '', $cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }
}

if (! function_exists('formatar_cnpj')) {
    /**
     * Formata CNPJ: 00.000.000/0000-00
     */
    function formatar_cnpj(string|null $cnpj): string
    {
        if (empty($cnpj)) {
            return '';
        }
        $cnpj = preg_replace('/\D/', '', $cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}

if (! function_exists('formatar_telefone')) {
    /**
     * Formata telefone BR: (00) 0000-0000 ou (00) 00000-0000
     */
    function formatar_telefone(string|null $tel): string
    {
        if (empty($tel)) {
            return '';
        }
        $tel = preg_replace('/\D/', '', $tel);
        if (strlen($tel) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $tel);
        }
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $tel);
    }
}

if (! function_exists('formatar_cep')) {
    /**
     * Formata CEP: 00000-000
     */
    function formatar_cep(string|null $cep): string
    {
        if (empty($cep)) {
            return '';
        }
        $cep = preg_replace('/\D/', '', $cep);
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }
}

if (! function_exists('data_br')) {
    /**
     * Formata data para formato brasileiro: dd/mm/aaaa
     */
    function data_br(string|\DateTimeInterface|null $data): string
    {
        if (empty($data)) {
            return '';
        }
        if ($data instanceof \DateTimeInterface) {
            return $data->format('d/m/Y');
        }
        return \Carbon\Carbon::parse($data)->format('d/m/Y');
    }
}

if (! function_exists('data_hora_br')) {
    /**
     * Formata data e hora para formato brasileiro: dd/mm/aaaa HH:ii
     */
    function data_hora_br(string|\DateTimeInterface|null $data): string
    {
        if (empty($data)) {
            return '';
        }
        if ($data instanceof \DateTimeInterface) {
            return $data->format('d/m/Y H:i');
        }
        return \Carbon\Carbon::parse($data)->format('d/m/Y H:i');
    }
}

if (! function_exists('data_banco')) {
    /**
     * Converte data BR (dd/mm/aaaa) para formato do banco (aaaa-mm-dd)
     */
    function data_banco(string|null $data): ?string
    {
        if (empty($data)) {
            return null;
        }
        return \Carbon\Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
    }
}

if (! function_exists('percentual')) {
    /**
     * Formata percentual com casas decimais.
     */
    function percentual(float|null $valor, int $casas = 2): string
    {
        return number_format((float) ($valor ?? 0), $casas, ',', '.') . '%';
    }
}

if (! function_exists('sigla_status_saude')) {
    /**
     * Retorna label do índice de saúde financeira.
     */
    function sigla_status_saude(int $indice): string
    {
        return match (true) {
            $indice >= 80 => 'Excelente',
            $indice >= 60 => 'Boa',
            $indice >= 40 => 'Atenção',
            $indice >= 20 => 'Crítica',
            default       => 'Emergencial',
        };
    }
}

if (! function_exists('cor_status_saude')) {
    /**
     * Retorna classe CSS Bootstrap/AdminLTE para o índice de saúde financeira.
     */
    function cor_status_saude(int $indice): string
    {
        return match (true) {
            $indice >= 80 => 'success',
            $indice >= 60 => 'info',
            $indice >= 40 => 'warning',
            $indice >= 20 => 'danger',
            default       => 'dark',
        };
    }
}

if (! function_exists('limpar_numero')) {
    /**
     * Remove tudo que não for dígito de uma string.
     */
    function limpar_numero(string|null $valor): string
    {
        return preg_replace('/\D/', '', (string) $valor);
    }
}

if (! function_exists('configuracao')) {
    /**
     * Retorna configuração do sistema a partir da tabela system_settings.
     */
    function configuracao(string $chave, mixed $padrao = null): mixed
    {
        static $cache = [];
        if (isset($cache[$chave])) {
            return $cache[$chave];
        }
        try {
            $valor = \App\Modules\Configuracoes\Models\Configuracao::where('chave', $chave)->value('valor');
            $cache[$chave] = $valor ?? $padrao;
            return $cache[$chave];
        } catch (\Throwable) {
            return $padrao;
        }
    }
}
