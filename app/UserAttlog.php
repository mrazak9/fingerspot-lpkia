<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAttlog extends Model
{
    protected $table = 'user_attlogs';

    protected $fillable = [
        'pin', 'name', 'scan_date', 'scan_time'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime: Y-m-d H:m:s',
    ];
}
