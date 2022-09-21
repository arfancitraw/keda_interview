<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ReportController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'staff_id' => 'required|integer',
            'type' => 'required|integer',
            'report' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $report = auth('api')->user();

        $idcust = $request->customer_id;
        $customer = User::where('id', $idcust)->first();

        $idstaff = $request->staff_id;
        $staff = User::where('id', $idstaff)->first();

        if (!$customer) {
            return response()->json([
                'status'    => false,
                'message'   => 'Customer user does not exist'
            ],404);
        }

        if (!$staff) {
            return response()->json([
                'status'    => false,
                'message'   => 'User staff does not exist'
            ],404);
        }

        if ($staff->user_type_id == 1) {
            return response()->json([
                'status'    => false,
                'message'   => 'Users are not included in the staff'
            ],403);
        }elseif($customer->user_type_id == 2){
            return response()->json([
                'status'    => false,
                'message'   => 'Users are not included in the customer'
            ],403);
        }

        if ($report->id == $customer->id) {
            return response()->json([
                'status'    => false,
                'message'   => 'Can`t send report for myself'
            ],402);
        }

        $message = Report::create([
            'reporter_id' => $report->id,
            'customer_id' => $idcust,
            'staff_id'    => $idstaff,
            'type'        => $request->type,
            'report'      => $request->report,
        ]);

        return response()->json([
            'status'    => true,
            'message'   => 'Report sent successfully',
            'detail'    => $message,
        ],200);
    }

    public function getAllReport()
    {
        $user = auth('api')->user();

        $report = Report::orderBy('id','desc')->get();
        
        if ($report->isEmpty()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Report not found'
            ],422);
        }

        return response()->json([
            'status' => true,
            'data'   => $report,
        ],200);  
    }
}
