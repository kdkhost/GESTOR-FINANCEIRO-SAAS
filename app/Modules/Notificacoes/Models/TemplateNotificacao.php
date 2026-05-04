<?php

namespace App\Modules\Notificacoes\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateNotificacao extends Model
{
    protected $table = 'notificacao_templates';

    protected $fillable = [
        'canal',
        'chave',
        'nome',
        'assunto',
        'conteudo',
        'variaveis',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'variaveis' => 'array',
        ];
    }
}

