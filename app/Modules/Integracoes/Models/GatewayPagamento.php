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
        'credenciais' => 'array',
        'configuracoes' => 'array',
        'ativo' => 'boolean',
        'sandbox' => 'boolean',
    ];

    public function getLogoUrlAttribute(): string
    {
        return asset('vendor/gateways/'.$this->identificador.'.svg');
    }

    public function getModoAttribute(): string
    {
        return $this->sandbox ? 'sandbox' : 'producao';
    }

    public function credential(string $key, mixed $default = null): mixed
    {
        return data_get($this->credenciais ?? [], $key, $default);
    }

    public function configuration(string $key, mixed $default = null): mixed
    {
        return data_get($this->configuracoes ?? [], $key, $default);
    }
}
