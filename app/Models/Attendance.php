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
}
