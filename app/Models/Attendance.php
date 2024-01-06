<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
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
        'email',
    ];

    protected $casts = [
        'in_am' => 'datetime:H:i',        // 'datetime:H:i A',
        'out_am' => 'datetime:H:i',
        'in_pm' => 'datetime:H:i',
        'out_pm' => 'datetime:H:i',
    ];


    public function getFullnameAttribute()
    {
        if(!isset($this->user))
        return null;
        return $this->user->first_name . " " . $this->user->last_name;
    }

    public function getEmailAttribute()
    {
        if(!isset($this->user))
        return null;
        return $this->user->email;
    }


    #   Relation: employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
