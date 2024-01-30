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
        'overtime',
        'deductions',
        'net',
        'net_total',
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

    #   Relation: user
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
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
        return $this->attendance->sum('total_hours');
    }

    public function getOvertimeAttribute()
    {
        return $this->attendance->sum('overtime');
    }

    public function getDeductionsAttribute()
    {
        $net = (float)($this->hours * $this->rate);
        $overtime = (float)($this->hours * $this->overtime);
        $netTotal = $net + $overtime;
        $tax_deduction = ($this->tax * $netTotal) / 100;
        return  number_format((float)$tax_deduction, 2, '.', '');
    }

    public function getNetAttribute()
    {
        $net = (float)($this->hours * $this->rate);
        $overtime = (float)($this->hours * $this->overtime);
        $netTotal = $net + $overtime;
        return $netTotal;
    }

    public function getNetTotalAttribute()
    {
        return $this->currency( $this->net - $this->deductions );
    }

    public function getTaxAttribute($value)
    {
        return 5;       // unit in percent
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
