<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{

    protected $fillable = [
        'title',
        'description',
    ];


    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
