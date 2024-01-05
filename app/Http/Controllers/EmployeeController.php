<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => Employee::all()
        ]);
    }

    public function show($id)
    {
        return Employee::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:80', 'unique:users'],
            "last_name" => ["required"],
        ]);

        // Validator
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 500);
        }

        $data = $request->all();

        $consumer = User::create($data);

        return response()->json([
            'message' => 'User created successfully!',
            'data'    => $consumer,
            'status'  => true,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $model = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:80'],
            "last_name"  => ["required"],
            "password"   => ["required","min:6"],
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

        return [
            'status' => $model->update($request->all()),
            'data'   => $model
        ];
    }

    public function destroy($id)
    {
        return User::findOrFail($id)->delete();
    }

}
