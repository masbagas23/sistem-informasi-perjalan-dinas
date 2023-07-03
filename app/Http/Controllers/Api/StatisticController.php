<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessTripApplication;
use App\Models\Customer;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function topCustomer()
    {
        $month = Carbon::now()->format('m');
        $customers = Customer::with(['applications:id,customer_id', 'applications.expense:id,application_id,total_nominal'])->whereHas('applications', function($q) use($month){
            $q->whereMonth('start_date', $month);
        })->get();
        $data = [];
        foreach ($customers as $customer) {
            $payload['y'] = $customer->applications->sum(function ($application) {
                return $application->expense ? $application->expense->total_nominal : 0;
            });
            $payload['x'] = $customer['name'];
            array_push($data, $payload);
        }
        return response()->json([
            "status"=>"success",
            "data"=>$data
        ], Response::HTTP_OK);
    }
}
