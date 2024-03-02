<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Overtime extends Model
{

    protected $table = 'overtime';

    protected $casts = [
        'date' => 'date',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    protected $fillable = [
        'date',
        'start',
        'end',
        'reason',
    ];

    protected $appends = [
        'startTime',
        'endTime',
    ];


    // Employee relation
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStartTimeAttribute($value)
    {
        return $this->start->format('h:i A');
    }

    public function getEndTimeAttribute($value)
    {
        return $this->end->format('h:i A');
    }
}
