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
        ->count();

        return response()->json([
            'TotalEmployees'    => Employee::all()->count(),
            'OnTimePercentage'  => $attendance,
            'OnTimeToday'       => '',
            'LateToday'         => '',
        ]);
    }

}
