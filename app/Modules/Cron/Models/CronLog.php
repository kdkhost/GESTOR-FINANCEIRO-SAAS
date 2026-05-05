<?php

namespace App\Modules\Cron\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronLog extends Model
{
    use HasFactory;

    protected $table = 'cron_logs';

    protected $fillable = [
        'cron_job_id',
        'status',
        'saida',
        'erro',
        'duracao_ms',
        'executado_em',
    ];

    protected $casts = [
        'duracao_ms' => 'integer',
        'executado_em' => 'datetime',
    ];

    public function cronJob(): BelongsTo
    {
        return $this->belongsTo(CronJob::class, 'cron_job_id');
    }
}
