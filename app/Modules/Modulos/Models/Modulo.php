<?php

namespace App\Modules\Modulos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
    use SoftDeletes;

    protected $table = 'modulos';

    protected $fillable = [
        'nome',
        'slug',
        'versao',
        'provider',
        'diretorio',
        'descricao',
        'ativo',
        'nativo',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'nativo' => 'boolean',
            'meta' => 'array',
        ];
    }
}

