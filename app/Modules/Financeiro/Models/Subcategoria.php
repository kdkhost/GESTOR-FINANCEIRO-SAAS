<?php

namespace App\Modules\Financeiro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subcategoria extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'categoria_id', 'nome', 'ativo'];
    protected function casts(): array { return ['ativo' => 'boolean', 'deleted_at' => 'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(\App\Modules\Usuarios\Models\User::class); }
    public function categoria(): BelongsTo { return $this->belongsTo(Categoria::class); }
    public function scopeAtivo($q) { return $q->where('ativo', true); }
}

