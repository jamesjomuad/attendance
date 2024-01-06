<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $casts = [
        'in_am' => 'datetime:H:i',        // 'datetime:H:i A',
        'out_am' => 'datetime:H:i',
        'in_pm' => 'datetime:H:i',
        'out_pm' => 'datetime:H:i',
    ];

    protected $fillable = [
        'in_am',
        'out_am',
        'in_pm',
        'out_pm',
        'status',
        'hours',
    ];

    protected $appends = [
        'fullname',
        'employee_code',
        'is_late',
    ];


    public function getFullnameAttribute()
    {
        if(!isset($this->employee))
        return null;
        return $this->employee->fullname;
    }

    public function getEmployeeCodeAttribute()
    {
        if(!isset($this->employee))
        return null;
        return $this->employee->code;
    }

    public function getIsLateAttribute()
    {
        return $this->in_am->gt($this->employee->schedule_in);
    }


    #   Relation: employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
