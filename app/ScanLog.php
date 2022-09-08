<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $table = 'scan_logs';

    protected $fillable = [
        'pin', 'scan', 'verify', 'status_scan'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s',
    ];
}
