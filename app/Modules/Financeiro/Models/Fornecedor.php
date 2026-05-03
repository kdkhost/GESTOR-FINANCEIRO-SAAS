<?php

namespace App\Modules\Financeiro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fornecedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fornecedores';

    protected $fillable = [
        'user_id', 'nome', 'tipo_pessoa', 'cpf_cnpj', 'email',
        'telefone', 'celular', 'cep', 'logradouro', 'numero',
        'complemento', 'bairro', 'cidade', 'estado', 'observacoes', 'ativo',
    ];

    protected function casts(): array
    {
        return ['ativo' => 'boolean', 'deleted_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Usuarios\Models\User::class);
    }

    public function scopeAtivo($query) { return $query->where('ativo', true); }
    public function scopeDoUsuario($query, int $userId) { return $query->where('user_id', $userId); }
}