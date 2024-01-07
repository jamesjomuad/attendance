<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;

class StatisticController extends Controller
{

    public function index()
    {
        $attendance = Attendance::whereDate('created_at', Carbon::now())
        ->whereNotNull('in_am')
        ->get();

        $OnTimeToday = $attendance->filter(function ($item) {
            return $item['is_late'] == false;
        })->count();

        $LateToday = $attendance->filter(function ($item) {
            return $item['is_late'] == true;
        })->count();

        $OnTimePercentage = ($OnTimeToday / $attendance->count()) * 100;

        return response()->json([
            'TotalEmployees'    => Employee::all()->count(),
            'OnTimePercentage'  => $OnTimePercentage,
            'OnTimeToday'       => $OnTimeToday,
            'LateToday'         => $LateToday,
        ]);
    }

}
