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
        $date = Carbon::parse(request()->filter_month);
        $customers = Customer::with(['applications:id,customer_id'])->whereHas('applications', function($query) use($date){
            $date = Carbon::parse(request()->filter_month);
            $query->whereMonth('start_date', $date)->whereYear('start_date', $date)->where('status', BusinessTripApplication::STATUS_APPROVE);
        })->get();
        $data = [];
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
        $date = Carbon::parse(request()->filter_month);
        $customers = Customer::with(['applications' => fn($query) => $query->whereMonth('start_date', $date)->whereYear('start_date', $date)->where('status', BusinessTripApplication::STATUS_APPROVE)])->whereHas('applications', function($query)use($date){
            $query->whereMonth('start_date', $date)->whereYear('start_date', $date)->where('status', BusinessTripApplication::STATUS_APPROVE);
        })->whereHas('applications.expense')->get();
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
