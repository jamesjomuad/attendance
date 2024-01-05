<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->get('per_page') ? : 50;

        $query = User::query()->whereDoesntHave('employee');

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
        return User::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:80', 'unique:users'],
            "last_name" => ["required"],
            "password" => ["required","min:6"],
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

        if( $request->filled('password') ){
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
