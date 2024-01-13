<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{

    protected $table = 'leave';

    protected $casts = [
        'start' => 'date',
        'end' => 'date',
    ];

    protected $fillable = [
        'request_id',
        'type',
        'start',
        'end',
        'reason',
        'status',
    ];

    protected $appends = [
        'totalDays',
        'startDate',
        'endDate',
    ];


    // Employee relation
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalDaysAttribute()
    {
        return $this->start->diffInDays($this->end);
    }

    public function getStartDateAttribute($value)
    {
        return $this->start->format('Y-m-d');
    }

    public function getEndDateAttribute($value)
    {
        return $this->end->format('Y-m-d');
    }
}
