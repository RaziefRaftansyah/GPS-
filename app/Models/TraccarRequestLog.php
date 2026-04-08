<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraccarRequestLog extends Model
{
    protected $fillable = [
        'method',
        'path',
        'content_type',
        'ip_address',
        'user_agent',
        'headers',
        'query_payload',
        'form_payload',
        'json_payload',
        'normalized_payload',
        'raw_body',
        'processed',
        'error_message',
        'location_id',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'query_payload' => 'array',
            'form_payload' => 'array',
            'json_payload' => 'array',
            'normalized_payload' => 'array',
            'processed' => 'boolean',
        ];
    }
}
