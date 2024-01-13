<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{

    protected $fillable = [
        'request_id',
        'type',
        'start',
        'end',
        'reason',
        'status',
    ];


    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
