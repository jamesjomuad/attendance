<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    protected $fillable = [
        'position',
        'department',
        'code',
        'type',
        'schedule_in',
        'schedule_out',
        'rate',
        'address',
        'phone',
        'dob',
        'is_active',
        'thumbnail',
        'gender',
    ];

    protected $appends = [
        'fullname',
        'email',
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

    #   Relation: user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    #   Relation: user
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
