<?php

namespace App\Modules\Saas\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'saas_planos';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'valor_mensal',
        'valor_anual',
        'limites',
        'ativo',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'valor_mensal' => 'decimal:2',
            'valor_anual' => 'decimal:2',
            'limites' => 'array',
            'ativo' => 'boolean',
            'ordem' => 'integer',
        ];
    }
}

