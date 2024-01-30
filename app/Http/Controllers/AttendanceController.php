<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Employee;

class AttendanceController extends Controller
{
    private function registerVaidation()
    {
        Validator::extend('is_am', function ($attribute, $value, $parameters) {
            if( $value ){
                $time = Carbon::today()->setTimeFromTimeString( $value );
                return $time->format('A') == 'AM';
            }
        });

        Validator::extend('is_pm', function ($attribute, $value, $parameters) {
            if( $value ){
                $time = Carbon::today()->setTimeFromTimeString( $value );
                return $time->format('A') == 'PM';
            }
        });
        return $this;
    }

    public function index(Request $request)
    {
        $per_page = $request->get('per_page') ? : 50;

        $query = Attendance::with('employee');

        //  Filter/Search
        $query->when($request->get('filter'), function($q) use ($request) {
            $filter = $request->get('filter');
            $q->orWhereHas( 'employee', function($employee) use($filter) {
                $employee->where( 'code', 'LIKE', "%$filter%" );
            });
            $q->orWhereHas( 'employee.user', function($user) use($filter) {
                $user->where( 'first_name', 'LIKE', "%$filter%" );
                $user->orWhere( 'last_name', 'LIKE', "%$filter%" );
                $user->orWhere( 'email', 'LIKE', "%$filter%" );
            });
            return $q;
        });

        //  Sort & Order
        $query->when($request->exists('sortBy') && $request->exists('orderBy'), function($q) use ($request) {
            $sortBy  = $request->get('sortBy');
            $orderBy = $request->get('orderBy');
            return $q->orderBy( $sortBy, $orderBy );
        });

        return response()->json($query->paginate($per_page));
    }

    public function show($id)
    {
        return Attendance::findOrFail($id);
    }

    public function store(Request $request)
    {
        $employee = Employee::findOrFail($request->input('employee'));

        $attendance = new Attendance();

        if( $request->filled('date') )
        $attendance->created_at = $request->input('date');
        if( $request->filled('in_am') )
        $attendance->in_am  = Carbon::today()->setTimeFromTimeString( $request->input('in_am') );
        if( $request->filled('out_am') )
        $attendance->out_am = Carbon::today()->setTimeFromTimeString( $request->input('out_am') );
        if( $request->filled('in_pm') )
        $attendance->in_pm  = Carbon::today()->setTimeFromTimeString( $request->input('in_pm') );
        if( $request->filled('out_pm') )
        $attendance->out_pm = Carbon::today()->setTimeFromTimeString( $request->input('out_pm') );

        $employee->attendance()->save($attendance);

        return response()->json([
            'message' => 'Attendace added successfully',
            'data'    => $attendance
        ], 201);

    }

    public function update(Request $request, $id)
    {
        // $this->registerVaidation();

        // $validator = Validator::make($request->input(), [
        //     'in_am'  => ['is_am'],
        //     'out_am' => ['is_am'],
        //     'in_pm'  => ['is_pm'],
        //     'out_pm' => ['is_pm'],
        // ],[
        //     'is_am' => 'Time must be in morning',
        //     'is_pm' => 'Time must be in afternoon',
        // ]);

        // // Validator
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'error' => $validator->errors()->first(),
        //     ], 500);
        // }

        $attendance = Attendance::findOrFail($id);

        if( $request->filled('in_am') )
        $attendance->in_am  = Carbon::today()->setTimeFromTimeString( $request->input('in_am') );
        if( $request->filled('out_am') )
        $attendance->out_am = Carbon::today()->setTimeFromTimeString( $request->input('out_am') );
        if( $request->filled('in_pm') )
        $attendance->in_pm  = Carbon::today()->setTimeFromTimeString( $request->input('in_pm') );
        if( $request->filled('out_pm') )
        $attendance->out_pm = Carbon::today()->setTimeFromTimeString( $request->input('out_pm') );

        $attendance->save();

        return response()->json([
            'message' => 'Attendace updated successfully',
            'data'    => $attendance
        ], 201);
    }

    public function destroy($id)
    {
        return Attendance::findOrFail($id)->delete();
    }

    public function login(Request $request)
    {
        $employee = Employee::where('code', $request->input('code'))->first();

        if( $employee===null ){
            return response()->json([
                'error' => 'Employee not found!'
            ], 500);
        }

        $today_attendances = $employee->attendance()
        ->whereDate('created_at', Carbon::today())
        ->get();

        // AM Login: Check employee if no login today
        if( $today_attendances->isEmpty() ){
            $attendance = new Attendance;

            $attendance->employee()->associate($employee);

            $attendance->in_am = Carbon::now();

            return response()->json([
                'status'   => $attendance->save(),
                'action'   => 'in_am',
                'employee' => $employee
            ]);
        }

        $attendance = $today_attendances->first();

        // PM Login
        $can_pm_login = $attendance->in_am != null
            && $attendance->out_am != null
            && $attendance->in_pm == null
        ;

        if( $can_pm_login ){
            $attendance->in_pm = Carbon::now();
            return response()->json([
                'status'   => $attendance->save(),
                'action'   => 'in_pm',
                'employee' => $employee
            ]);
        }

        $completed_today = $attendance->in_am !== null
            && $attendance->out_am !== null
            && $attendance->in_pm !== null
            && $attendance->out_pm !== null
        ;

        if($completed_today){
            return response()->json([
                'error' => "Already completed today's shift!"
            ], 500);
        }else{
            return response()->json([
                'error' => 'Already login!'
            ], 500);
        }

    }

    public function logout(Request $request)
    {
        $employee = Employee::where('code', $request->input('code'))->first();

        // Employee not found
        if( $employee===null ){
            return response()->json([
                'error' => 'Employee not found!'
            ], 500);
        }

        $today_attendances = $employee->attendance()
        ->whereDate('created_at', Carbon::today())
        ->get();

        $attendance = $today_attendances->first();

        // Login not found
        if($today_attendances->isEmpty()){
            return response()->json([
                'error' => 'No Time In found!'
            ], 500);
        }

        $can_logout_in_am = $today_attendances->first()->in_am!=NULL && $today_attendances->first()->out_am==NULL;

        // AM logout
        if( $can_logout_in_am ){
            $attendance->out_am = Carbon::now();
            return response()->json([
                'status'   => $attendance->save(),
                'action'   => 'am_out',
                'employee' => $employee
            ]);
        }elseif($attendance->in_pm==NULL){
            return response()->json([
                'error' => 'You need to login for afternoon!'
            ], 500);
        }

        $can_logout_in_pm = $attendance->in_am != NULL
            && $attendance->out_am != NULL
            && $attendance->in_pm != NULL
            && $attendance->out_pm == NULL
        ;

        // PM logout
        // Compute the total hours
        if( $can_logout_in_pm ){
            $attendance->out_pm = Carbon::now();

            $total_am = $attendance->in_am->diff($attendance->out_am)->h + ($attendance->in_am->diff($attendance->out_am)->i / 60);
            $total_pm = $attendance->in_pm->diff($attendance->out_pm)->h + ($attendance->in_pm->diff($attendance->out_pm)->i / 60);

            $attendance->hours = round($total_am + $total_pm, 2);

            return response()->json([
                'status'     => $attendance->save(),
                'action'     => 'pm_out',
                'attendance' => $attendance,
                'employee'   => $employee
            ]);
        }else{
            return response()->json([
                'error' => 'Already logout!'
            ], 500);
        }

    }

    public function qrcode(Request $request)
    {
        $employee = Employee::where('code', $request->input('code'))->first();

        if( $employee===null ){
            return response()->json([
                'error' => 'Employee not found!'
            ], 500);
        }

        $attendances = $employee->attendance()
        ->whereDate('created_at', Carbon::today())
        ->get();

        $attendance = $attendances->first();

        // Init Attendance
        if( $attendances->isEmpty() ){
            $attendance = new Attendance;

            $attendance->employee()->associate($employee);

            if( Carbon::now()->format('H') < 12 ){
                $attendance->in_am = Carbon::now();
            }else{
                $attendance->in_pm = Carbon::now();
            }

            return response()->json([
                'status'   => $attendance->save(),
                'message'   => 'Goodmorning '.$employee->fullname,
                'employee' => $employee
            ]);
        }

        if( $attendance->in_am!=null && $attendance->out_am==null )
        {
            $attendance->out_am = Carbon::now();

            return response()->json([
                'status'   => $attendance->save(),
                'message'  => $employee->fullname .', Lunch break',
                'employee' => $employee
            ]);
        }
        elseif( $attendance->out_am!=null && $attendance->in_pm==null )
        {
            $attendance->in_pm = Carbon::now();

            return response()->json([
                'status'   => $attendance->save(),
                'message'  => $employee->fullname . ', logging in for the afternoon.',
                'employee' => $employee
            ]);
        }
        elseif( $attendance->in_pm!=null && $attendance->out_pm==null )
        {
            $attendance->out_pm = Carbon::now();

            return response()->json([
                'status'   => $attendance->save(),
                'message'  => $employee->fullname . ', logging out for the afternoon.',
                'employee' => $employee
            ]);
        }

        return response()->json([
            'message'   => "That's all for today!",
            'employee' => $employee
        ]);
    }
}
