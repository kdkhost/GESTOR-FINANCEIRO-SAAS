<?php

namespace App\Modules\Financeiro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContaPagar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contas_pagar';

    protected $fillable = [
        'user_id', 'recorrencia_id', 'fornecedor_id', 'categoria_id',
        'subcategoria_id', 'conta_bancaria_id', 'centro_custo_id',
        'forma_pagamento_id', 'descricao', 'valor', 'valor_pago',
        'juros', 'multa', 'desconto', 'data_vencimento', 'data_pagamento',
        'status', 'numero_parcela', 'total_parcelas', 'codigo_barras',
        'numero_documento', 'observacoes', 'recorrente',
    ];

    protected function casts(): array
    {
        return [
            'valor'            => 'decimal:2',
            'valor_pago'       => 'decimal:2',
            'juros'            => 'decimal:2',
            'multa'            => 'decimal:2',
            'desconto'         => 'decimal:2',
            'data_vencimento'  => 'date',
            'data_pagamento'   => 'date',
            'recorrente'       => 'boolean',
            'deleted_at'       => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ContaPagar $conta) {
            // Atualiza status automaticamente se vencida
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

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
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

    public function centroCusto(): BelongsTo
    {
        return $this->belongsTo(CentroCusto::class);
    }

    public function recorrencia(): BelongsTo
    {
        return $this->belongsTo(Recorrencia::class);
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(\App\Modules\Financeiro\Models\Anexo::class, 'entidade_id')
            ->where('entidade_tipo', 'contas_pagar');
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
            ->whereIn('status', ['pendente', 'parcialmente_pago']);
    }

    public function scopeVencendoEm($query, int $dias)
    {
        return $query->whereBetween('data_vencimento', [today(), today()->addDays($dias)])
            ->whereIn('status', ['pendente', 'parcialmente_pago']);
    }

    public function scopePorPeriodo($query, ?string $inicio, ?string $fim)
    {
        if ($inicio) {
            $query->whereDate('data_vencimento', '>=', $inicio);
        }
        if ($fim) {
            $query->whereDate('data_vencimento', '<=', $fim);
        }
        return $query;
    }

    // Atributos
    public function getValorFormatadoAttribute(): string
    {
        return moeda_br($this->valor);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'           => 'Pendente',
            'pago'               => 'Pago',
            'vencido'            => 'Vencido',
            'cancelado'          => 'Cancelado',
            'parcialmente_pago'  => 'Parcialmente Pago',
            default              => ucfirst($this->status),
        };
    }

    public function getStatusCorAttribute(): string
    {
        return match ($this->status) {
            'pago'               => 'success',
            'vencido'            => 'danger',
            'cancelado'          => 'secondary',
            'parcialmente_pago'  => 'warning',
            default              => 'info',
        };
    }

    public function getValorEfetivoAttribute(): float
    {
        return (float) ($this->valor + $this->juros + $this->multa - $this->desconto);
    }
}
