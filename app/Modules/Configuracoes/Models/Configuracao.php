<?php

namespace App\Modules\Configuracoes\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'grupo', 'chave', 'valor', 'tipo',
        'label', 'descricao', 'sensivel', 'visivel',
    ];

    protected function casts(): array
    {
        return ['sensivel' => 'boolean', 'visivel' => 'boolean'];
    }

    // -------------------------------------------------------
    // Acessores
    // -------------------------------------------------------

    public function getValorCasteadoAttribute(): mixed
    {
        return match ($this->tipo) {
            'booleano' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'numero'   => (float) $this->valor,
            'json'     => json_decode($this->valor, true),
            default    => $this->valor,
        };
    }

    // -------------------------------------------------------
    // Helpers estáticos
    // -------------------------------------------------------

    public static function obter(string $chave, mixed $padrao = null): mixed
    {
        return static::where('chave', $chave)->value('valor') ?? $padrao;
    }

    public static function definir(string $chave, mixed $valor, string $grupo = 'geral'): void
    {
        static::updateOrCreate(
            ['chave' => $chave],
            ['valor' => $valor, 'grupo' => $grupo]
        );
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeDoGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    public function scopeVisiveis($query)
    {
        return $query->where('visivel', true);
    }
}
