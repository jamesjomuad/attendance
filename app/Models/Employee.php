<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    protected $table = 'employees';

    protected $casts = [
        'schedule_in' => 'datetime',      // datetime:H:i A
        'schedule_out' => 'datetime',
    ];

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
        // 'dtr'
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
        return $this->hasMany(Attendance::class);
    }

    #   Relation: position
    public function position()
    {
        return $this->belongsTo(Position::class, 'position');
    }

    #   Relation: leave
    public function leave()
    {
        return $this->hasMany(Leave::class);
    }

    public function getDtrAttribute()
    {
        $attendances = $this->attendance->mapWithKeys(function($attendance){
            return [(string)$attendance->created_at->format('j') => $attendance];
        });

        $startDate  = Carbon::now()->startOfMonth();

        // Get the number of days in the month
        $numberOfDaysInMonth = Carbon::now()->daysInMonth;

        // Create an array to store the days
        $daysArray = collect([]);

        // Loop through each day and add it to the array
        for ($i = 0; $i < $numberOfDaysInMonth; $i++) {
            $daysArray->push( $startDate ->copy()->addDays($i)->format('j') );
        }

        $timesheet = $daysArray->mapWithKeys(function($item) use($attendances) {
            if( $attendances->has($item) )
                return ["$item" => $attendances->get($item)];
            else
                return ["$item" => ""];
        })->sortBy('day')->toArray();

        return [
            'month' => Carbon::now()->format('F'),
            'days'  => $timesheet
        ];
    }

    #
    #   Scopes
    #
    public function scopeDtr()
    {
        $this->appends[] = 'dtr';
    }
}
