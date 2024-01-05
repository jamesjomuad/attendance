<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->get('per_page') ? : 50;

        $query = Employee::with('user');

        //  Sort & Order
        $query->when($request->exists('sortBy') && $request->exists('orderBy'), function($q) use ($request) {
            $sortBy  = $request->get('sortBy');
            $orderBy = $request->get('orderBy');
            return $q->orderBy( $sortBy, $orderBy );
        });

        //  Filter/Search
        $query->when($request->get('filter'), function($q) use ($request) {
            $filter = $request->get('filter');
            $q->where( 'first_name', 'LIKE', "%$filter%" );
            $q->orWhere( 'last_name', 'LIKE', "%$filter%" );
            $q->orWhere( 'email', 'LIKE', "%$filter%" );
            return $q;
        });

        return response()->json($query->paginate($per_page));
    }

    public function show($id)
    {
        return Employee::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'  => ['required', 'string', 'max:80', 'unique:users'],
            "last_name" => ["required"],
            "rate"      => ["required"],
        ]);

        // Validator
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 500);
        }

        $user = User::create([
            'username'   => $request->username,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->username)
        ]);

        $user->employee()->create([
            'position'     => $request->position,
            'department'   => 0,
            'type'         => 0,
            'code'         => $request->position . '-' . Carbon::now()->timestamp . Str::random(4),
            'schedule_in'  => date('Y-m-d H:i:s', strtotime($request->schedule_in)),
            'schedule_out' => date('Y-m-d H:i:s', strtotime($request->schedule_out)),
            'gender'       => $request->gender,
            'rate'         => $request->rate,
            'address'      => $request->address,
            'phone'        => $request->phone,
        ]);

        return response()->json([
            'message' => 'Employee created successfully',
            'data'    => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $model = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:80'],
            "last_name"  => ["required"],
            "rate"       => ["required"],
        ]);

        // Validator
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 500);
        }


        if( $request->filled('password') ){
            $model->password = Hash::make($request->password);
            return response()->json([
                'status' => $model->save()
            ]);
        }

        $model->employee()->update([
            'position'     => $request->position,
            'department'   => 0,
            'type'         => 0,
            'code'         => $request->position . '-' . Carbon::now()->timestamp . Str::random(4),
            'schedule_in'  => date('Y-m-d H:i:s', strtotime($request->schedule_in)),
            'schedule_out' => date('Y-m-d H:i:s', strtotime($request->schedule_out)),
            'gender'       => $request->gender,
            'rate'         => $request->rate,
            'address'      => $request->address,
            'phone'        => $request->phone,
        ]);

        return response()->json([
            'message' => 'Employee updated successfully',
            'data'    => $model
        ], 201);
    }

    public function destroy($id)
    {
        return Employee::findOrFail($id)->delete();
    }

}
