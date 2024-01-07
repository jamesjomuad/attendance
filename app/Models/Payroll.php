<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Payroll extends Employee
{
    protected $table = 'employees';

    protected $appends = [
        'fullname',
        'hours',
        'deductions',
        'net',
    ];

    #
    #   Relations
    #

    #   Relation: user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    #   Relation: user
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }


    #
    #   Attributes
    #
    public function getFullnameAttribute()
    {
        if(!isset($this->user))
        return null;
        return $this->user->first_name . " " . $this->user->last_name;
    }

    public function getHoursAttribute()
    {
        return $this->attendance->sum('total_hours');
    }

    public function getDeductionsAttribute()
    {
        return 0;
    }

    public function getNetAttribute()
    {
        return $this->hours * $this->rate;
    }

}