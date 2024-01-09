<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipController extends Controller
{

    public function index(Request $request)
    {
        return Payroll::findOrFail();
    }
    public function show($id, Request $request)
    {
        $date = [
            'from' => Carbon::now()->startOfMonth()->format('F d, Y'),
            'to' => Carbon::now()->endOfMonth()->format('F d, Y'),
        ];

        $employee = Payroll::findOrFail($id);

        // Generate PDF view
        $pdf = Pdf::loadView('payslip', compact('employee','date'));

        if( $request->input('format')=='html' ){
            return view('payslip', compact('employee','date'));
        }else if( $request->input('format')=='download' ){
            return $pdf->download("payslip - $employee->fullname - ".$date['from']." - ".$date['to'].".pdf");
        }else{
            // Display the PDF in the browser
            return $pdf->stream();
        }
    }
}
