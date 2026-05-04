<?php

namespace App\Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fatura extends Model
{
    protected $table = 'saas_faturas';

    protected $fillable = [
        'empresa_id',
        'assinatura_id',
        'status',
        'competencia',
        'valor',
        'vencimento_em',
        'pago_em',
        'gateway',
        'gateway_ref',
        'link_pagamento',
        'pix_copia_e_cola',
        'boleto_linha_digitavel',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'vencimento_em' => 'datetime',
            'pago_em' => 'datetime',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(Assinatura::class, 'assinatura_id');
    }
}
