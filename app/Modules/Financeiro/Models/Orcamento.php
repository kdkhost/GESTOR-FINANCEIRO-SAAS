<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Orcamento extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'orcamentos';
    protected $fillable = ['user_id','categoria_id','nome','valor_limite','mes','ano','alertar_em'];
    protected function casts(): array { return ['valor_limite'=>'decimal:2','alertar_em'=>'integer','deleted_at'=>'datetime']; }
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
}