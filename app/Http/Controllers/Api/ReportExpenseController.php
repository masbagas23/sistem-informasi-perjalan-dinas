<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\BusinessTripApplication;
use App\Models\ExpenseDetail as Model;
use Carbon\Carbon;
use Illuminate\Http\Response;
use PDF;

class ReportExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Model::whereHas('expense.application', function($query){
                $query->where('status', BusinessTripApplication::RESULT_DONE)->whereMonth('start_date', Carbon::parse(request()->month));
            })->with(['expense:id,application_id','expense.application:id,code,customer_id','expense.application.customer:id,name', 'costCategory:id,name']);

            if (isset(request()->order_column)){
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function($query){
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
                });
            }

            $data = $data->paginate(request()->per_page);
            return new DataCollection($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>'error',
                'message'=>$th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = BusinessTripApplication::find($id);
            $data->load('expense.details.costCategory');
            return response()->json([
                "status"=>"success",
                "data"=>$data
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>'error',
                'message'=>$th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function print()
    {
        $data = Model::whereHas('expense.application', function($query){
            $query->where('status', BusinessTripApplication::RESULT_DONE)->whereMonth('start_date', Carbon::parse(request()->month));
        })->with(['expense:id,application_id','expense.application:id,code,customer_id','expense.application.customer:id,name', 'costCategory:id,name'])
        ->where('status', Model::STATUS_VALID);
        $month = '';
        $year = '';
        if(request()->month){
            $date = Carbon::parse(request()->month);
            $month = $date->translatedFormat('F');
            $year = $date->format('Y');
            $data = $data->whereMonth('created_at', $date)->whereYear('created_at', $date);
        }
        $total = $data->sum('nominal');
        $data = $data->get();
        $pdf = PDF::loadView('pdf.report-expenses', compact(['data','month','year','total']))
            ->setPaper('a4', 'landscape');

        return $pdf->stream();
    }
}
