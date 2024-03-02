<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use PhpParser\Node\Expr\FuncCall;

class Payroll extends Employee
{
    protected $table = 'employees';

    protected $appends = [
        'fullname',
        'rateCurrency',
        'hours',
        'tax_deductions',
        'net',
        'net_total',
        'overtime_hours',
        'overtime_net',
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

    #   Relation: position
    public function position()
    {
        return $this->belongsTo(Position::class, 'position');
    }

    #   Relation: Leaves
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

    #   Relation: Overtime
    public function overtime()
    {
        return $this->hasMany(Overtime::class, 'employee_id');
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

    public function getRateCurrencyAttribute()
    {
        return $this->currency($this->rate);
    }

    public function getHoursAttribute()
    {
        return number_format((float)$this->attendance->sum('total_hours'), 2, '.', '');
    }

    public function getTaxDeductionsAttribute()
    {
        $net = (float)($this->hours * $this->rate);
        $tax_deduction = ($this->tax * $net) / 100;
        return  number_format((float)$tax_deduction, 2, '.', '');
    }

    public function getNetAttribute()
    {
        $net = (float)($this->hours * $this->rate);
        return number_format((float)$net, 2, '.', '');
    }

    public function getNetTotalAttribute()
    {
        $net_deductions = $this->net - $this->tax_deductions;

        $total = $net_deductions + $this->overtime_net;

        return $this->currency( $total );
    }

    public function getTaxAttribute($value)
    {
        return 5;       // unit in percent
    }

    public function getOvertimeHoursAttribute()
    {
        return (float)$this->overtime->sum('totalHours');
    }

    public function getOvertimeNetAttribute()
    {
        return (float)($this->rate * $this->overtime->sum('totalHours'));
    }




    #
    #   Scopes
    #
    public function scopeBiMonth($query, $dates)
    {
        $query = $query->whereHas('attendance', function ($attendance) use($dates) {
            return $attendance->whereBetween('created_at', [$dates['from'], $dates['to']]);
        });
        return $query;
    }

    #
    #   Helper
    #
    private function currency($amount)
    {
        $numberFormatter = new \NumberFormatter( 'en_US', \NumberFormatter::CURRENCY );
        return $numberFormatter->formatCurrency($amount, "PHP" ) ;
    }
}
