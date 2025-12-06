<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStat extends Model
{
    protected $table = 'request_stats';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'temperature',
        'response_time_ms',
        'success',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'created_at' => 'datetime',
    ];
}
