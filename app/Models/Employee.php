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
    protected $appends = [
        'fullname',
    ];


    public function getFullnameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }


    #   Relation: user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
