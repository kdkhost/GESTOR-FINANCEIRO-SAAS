<?php

namespace App\Modules\Financeiro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContaBancaria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contas_bancarias';

    protected $fillable = [
        'user_id', 'banco_id', 'nome', 'agencia', 'numero_conta',
        'digito', 'tipo', 'saldo_inicial', 'saldo_atual',
        'incluir_no_total', 'ativo', 'cor', 'icone', 'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'saldo_inicial'    => 'decimal:2',
            'saldo_atual'      => 'decimal:2',
            'incluir_no_total' => 'boolean',
            'ativo'            => 'boolean',
            'deleted_at'       => 'datetime',
        ];
    }

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Usuarios\Models\User::class);
    }

    public function banco(): BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

    public function contasPagar(): HasMany
    {
        return $this->hasMany(ContaPagar::class);
    }

    public function contasReceber(): HasMany
    {
        return $this->hasMany(ContaReceber::class);
    }

    public function receitas(): HasMany
    {
        return $this->hasMany(Receita::class);
    }

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class);
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDoUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Utilitários
    public function getSaldoFormatadoAttribute(): string
    {
        return moeda_br($this->saldo_atual);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'corrente'     => 'Conta Corrente',
            'poupanca'     => 'Poupança',
            'salario'      => 'Conta Salário',
            'investimento' => 'Investimento',
            'carteira'     => 'Carteira',
            default        => 'Outro',
        };
    }
}
