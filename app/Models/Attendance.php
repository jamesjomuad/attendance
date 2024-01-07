<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'total_hours',
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

    public function getTotalHoursAttribute()
    {
        if( $this->in_am!==null && $this->out_am === null ){
            $total = $this->in_am->diff(Carbon::now())->h + ($this->in_am->diff(Carbon::now())->i / 60);
            return round($total, 2);
        }
        elseif( $this->in_pm!==null && $this->out_pm === null )
        {
            $total = $this->in_pm->diff(Carbon::now())->h + ($this->in_pm->diff(Carbon::now())->i / 60);
            return round($total, 2);
        }

        if( in_array(null, [$this->in_am,$this->out_am,$this->in_pm,$this->out_pm], true) ){
            return 0;
        }

        $total_am = $this->in_am->diff($this->out_am)->h + ($this->in_am->diff($this->out_am)->i / 60);
        $total_pm = $this->in_pm->diff($this->out_pm)->h + ($this->in_pm->diff($this->out_pm)->i / 60);
        return  round($total_am + $total_pm, 2);
    }


    #   Relation: employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    #
    #   Scopes
    #
    public function scopeThisMonth($query)
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

}
