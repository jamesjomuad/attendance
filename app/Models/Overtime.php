<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{

    protected $table = 'overtime';

    protected $casts = [
        'start' => 'date',
        'end' => 'date',
    ];

    protected $fillable = [
        'start',
        'end',
        'reason',
    ];


    // Employee relation
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
