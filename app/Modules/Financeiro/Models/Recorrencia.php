<?php
namespace App\Modules\Financeiro\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Recorrencia extends Model {
    use HasFactory, SoftDeletes;
    protected $table = 'recorrencias';
    protected $fillable = ['user_id','descricao','tipo','valor','dia_vencimento','data_inicio','data_fim','categoria_id','conta_bancaria_id','ativo'];
    protected function casts(): array { return ['valor'=>'decimal:2','data_inicio'=>'date','data_fim'=>'date','ativo'=>'boolean','deleted_at'=>'datetime']; }
    public function categoria() { return $this->belongsTo(Categoria::class); }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id', $id); }
}