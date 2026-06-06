<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $table = 'webhook_logs';

    public $timestamps = false;

    protected $fillable = [
        'event_type',
        'payload_json',
        'processed',
        'created_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'processed'    => 'boolean',
        'created_at'   => 'datetime',
    ];
}
