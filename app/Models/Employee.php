<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

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
}
