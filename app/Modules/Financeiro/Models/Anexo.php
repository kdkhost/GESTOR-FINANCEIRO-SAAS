<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Anexo extends Model {
    use HasFactory;
    protected $table = 'anexos';
    protected $fillable = ['user_id','entidade_tipo','entidade_id','nome_original','nome_arquivo','caminho','tamanho','mime_type'];
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
}