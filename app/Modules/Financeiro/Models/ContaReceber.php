<?php

namespace App\Modules\Financeiro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContaReceber extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contas_receber';

    protected $fillable = [
        'user_id', 'recorrencia_id', 'cliente_id', 'categoria_id',
        'subcategoria_id', 'conta_bancaria_id', 'forma_pagamento_id',
        'descricao', 'valor', 'valor_recebido', 'juros', 'multa',
        'desconto', 'data_vencimento', 'data_recebimento', 'status',
        'numero_parcela', 'total_parcelas', 'numero_documento',
        'observacoes', 'recorrente',
    ];

    protected function casts(): array
    {
        return [
            'valor'             => 'decimal:2',
            'valor_recebido'    => 'decimal:2',
            'juros'             => 'decimal:2',
            'multa'             => 'decimal:2',
            'desconto'          => 'decimal:2',
            'data_vencimento'   => 'date',
            'data_recebimento'  => 'date',
            'recorrente'        => 'boolean',
            'deleted_at'        => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ContaReceber $conta) {
            if ($conta->status === 'pendente' && $conta->data_vencimento->isPast()) {
                $conta->status = 'vencido';
            }
        });
    }

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Usuarios\Models\User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria(): BelongsTo
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function contaBancaria(): BelongsTo
    {
        return $this->belongsTo(ContaBancaria::class);
    }

    public function recorrencia(): BelongsTo
    {
        return $this->belongsTo(Recorrencia::class);
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Anexo::class, 'entidade_id')
            ->where('entidade_tipo', 'contas_receber');
    }

    // Scopes
    public function scopeDoUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeVencidas($query)
    {
        return $query->where('status', 'vencido')
            ->orWhere(fn ($q) => $q->where('status', 'pendente')->whereDate('data_vencimento', '<', today()));
    }

    public function scopeVencendoHoje($query)
    {
        return $query->whereDate('data_vencimento', today())
            ->whereIn('status', ['pendente', 'parcialmente_recebido']);
    }

    public function scopeVencendoEm($query, int $dias)
    {
        return $query->whereBetween('data_vencimento', [today(), today()->addDays($dias)])
            ->whereIn('status', ['pendente', 'parcialmente_recebido']);
    }

    // Atributos
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'               => 'Pendente',
            'recebido'               => 'Recebido',
            'vencido'                => 'Vencido',
            'cancelado'              => 'Cancelado',
            'parcialmente_recebido'  => 'Parcialmente Recebido',
            default                  => ucfirst($this->status),
        };
    }

    public function getStatusCorAttribute(): string
    {
        return match ($this->status) {
            'recebido'               => 'success',
            'vencido'                => 'danger',
            'cancelado'              => 'secondary',
            'parcialmente_recebido'  => 'warning',
            default                  => 'info',
        };
    }

    public function getValorEfetivoAttribute(): float
    {
        return (float) ($this->valor + $this->juros + $this->multa - $this->desconto);
    }
}
