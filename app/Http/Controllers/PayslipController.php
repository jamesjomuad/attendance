<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $date = $this->dateInterval();

        $employees = Payroll::with('position')->get();

        $pdf = Pdf::loadView('payslips', compact('employees','date'));

        if( $request->input('format')=='html' ){
            return view('payslips', compact('employees','date'));
        }else if( $request->input('format')=='download' ){
            return $pdf->download("payslips - ".$date['from']." - ".$date['to'].".pdf");
        }else if( $request->input('format')=='json' ){
            return response()->json($employees);
        }else{
            return $pdf->stream();      // Display the PDF in the browser
        }
    }

    public function show($id, Request $request)
    {
        $dates = $this->dateInterval();

        $employee = Payroll::with([
            'position',
            'leaves' => function($leaves) use($dates) {
                $leaves->whereBetween('start', [ $dates['from'], $dates['to'] ]);
                $leaves->whereBetween('end', [ $dates['from'], $dates['to'] ]);
            },
            'overtime' => function($overtime) use($dates) {
                $overtime->whereBetween('date', [ $dates['from'], $dates['to'] ]);
            },
            'attendance' => function($q) use($dates) {
                $q->whereBetween('created_at', [ $dates['from'], $dates['to'] ]);
            }])
        ->findOrFail($id);

        dd(
            $employee
            ->toArray()
        );

        // Generate PDF view
        $pdf = Pdf::loadView('payslip', compact('employee','dates'));

        if( $request->input('format')=='html' ){
            return view('payslip', compact('employee','dates'));
        }else if( $request->input('format')=='download' ){
            return $pdf->download("payslip - $employee->fullname - ".$dates['from']." - ".$dates['to'].".pdf");
        }else if( $request->input('format')=='json' ){
            return response()->json($employee);
        }else{
            // Display the PDF in the browser
            return $pdf->stream();
        }
    }

    private function dateInterval()
    {
        $today = Carbon::now();

        if( $today->day < 15 ){
            $date = [
                'from' => Carbon::now()->startOfMonth(),
                'to'   => Carbon::now()->startOfMonth()->addDays(14)
            ];
        } else {
            $date = [
                'from' => Carbon::now()->startOfMonth()->addDays(15),
                'to'   => Carbon::now()->endOfMonth(),
            ];
        }

        return $date;
    }
}
