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
    public function topCustomerTrip()
    {
        $customers = Customer::with(['applications:id,customer_id'])->whereHas('applications')->get();
        $data = [];
        $date = Carbon::parse(request()->filter_month);
        foreach ($customers as $customer) {
            // $payload['y'] = $customer->applications->sum(function ($application) {
            //     return $application->expense ? $application->expense->total_nominal : 0;
            // });
            $payload['y'] = $customer->applications()->whereMonth('start_date', $date)->whereYear('start_date', $date)->where('status', BusinessTripApplication::STATUS_APPROVE)->count();
            $payload['x'] = $customer['name'];
            array_push($data, $payload);
        }
        return response()->json([
            "status"=>"success",
            "data"=>$data
        ], Response::HTTP_OK);
    }

    public function topCustomerCost()
    {
        $customers = Customer::with(['applications:id,customer_id', 'applications.expense:id,application_id,total_nominal'])->whereHas('applications.expense')->get();
        $data = [];
        $date = Carbon::parse(request()->filter_month);
        foreach ($customers as $customer) {           
            $payload['y'] = $customer->applications()->whereMonth('start_date', $date)->whereYear('start_date', $date)->where('status', BusinessTripApplication::STATUS_APPROVE)->sum(function ($application) {
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
