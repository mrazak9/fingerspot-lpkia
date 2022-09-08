<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employes';

    protected $fillable = [
        'pin', 'name', 'privilege', 'finger', 'face', 'password', 'rfid', 'vein'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s',
    ];
}
