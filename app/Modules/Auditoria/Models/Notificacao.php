<?php
namespace App\Modules\Auditoria\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Notificacao extends Model {
    use HasFactory;
    protected $table = 'notificacoes';
    protected $fillable = ['user_id','titulo','mensagem','tipo','lida','lida_em','dados'];
    protected function casts(): array {
        return ['lida'=>'boolean','lida_em'=>'datetime','dados'=>'array'];
    }
    public function scopeDoUsuario($q, int $id) { return $q->where('user_id',$id); }
}