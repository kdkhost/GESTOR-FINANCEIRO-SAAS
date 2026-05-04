<?php

namespace App\Modules\Integracoes\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayPagamento extends Model
{
    protected $table = 'gateways_pagamento';

    protected $fillable = [
        'nome',
        'identificador',
        'credenciais',
        'configuracoes',
        'ativo',
        'sandbox',
    ];

    protected $casts = [
        'credenciais'     => 'array',
        'configuracoes'   => 'array',
        'ativo'           => 'boolean',
        'sandbox'         => 'boolean',
    ];
}
