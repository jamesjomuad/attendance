<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use App\Models\Overtime;
use App\Models\Employee;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->get('per_page') ? : 50;

        $query = Overtime::with(['employee']);

        //  Filter/Search
        $query->when($request->get('filter'), function($q) use ($request) {
            $filter = $request->get('filter');
            $q->where( 'first_name', 'LIKE', "%$filter%" );
            $q->orWhere( 'last_name', 'LIKE', "%$filter%" );
            $q->orWhere( 'code', 'LIKE', "%$filter%" );
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
        try{
            return Overtime::with('employee')->findOrFail($id);
        }catch(Exception $e){
            return response()->json(['error' => 'INVALID_ROUTE'], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "employee" => ["required"],
            "date"     => ["required"],
            "start"    => ["required"],
            "end"      => ["required"],
            "reason"   => ["required"],
        ]);

        // Validator
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 500);
        }

        $employee = Employee::find($request->input('employee'));

        $employee = $employee->overtime()->create( $request->input() );

        return response()->json([
            'message' => 'Overtime created successfully',
            'data'    => $employee
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $overtime = Overtime::find($id);

        $validator = Validator::make($request->all(), [
            "employee" => ["required"],
            "start"    => ["required"],
            "end"      => ["required"],
            "reason"   => ["required"],
        ]);

        // Validator
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 500);
        }

        $overtime->update( $request->input() );

        return response()->json([
            'message' => 'Employee created successfully',
            'data'    => $overtime
        ], 201);
    }

    public function destroy($id)
    {
        return Overtime::findOrFail($id)->delete();
    }

}
