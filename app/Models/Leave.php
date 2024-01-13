<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{

    protected $table = 'leave';

    protected $fillable = [
        'request_id',
        'type',
        'start',
        'end',
        'reason',
        'status',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
