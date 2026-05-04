<?php

namespace App\Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    protected $table = 'saas_assinaturas';

    protected $fillable = [
        'empresa_id',
        'plano_id',
        'status',
        'inicio_em',
        'fim_em',
        'proxima_cobranca_em',
        'gateway',
        'gateway_ref',
        'trial_ate',
        'cancelada_em',
        'cancelamento_motivo',
    ];

    protected function casts(): array
    {
        return [
            'inicio_em' => 'datetime',
            'fim_em' => 'datetime',
            'proxima_cobranca_em' => 'datetime',
            'trial_ate' => 'datetime',
            'cancelada_em' => 'datetime',
        ];
    }
}

