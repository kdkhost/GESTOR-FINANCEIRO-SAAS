<?php

namespace App\Modules\Financeiro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'nome', 'tipo', 'icone', 'cor', 'ativo'];

    protected function casts(): array
    {
        return ['ativo' => 'boolean', 'deleted_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Usuarios\Models\User::class);
    }

    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDoUsuario($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDoTipo($query, string $tipo)
    {
        return $query->where(fn ($q) => $q->where('tipo', $tipo)->orWhere('tipo', 'ambos'));
    }
}
