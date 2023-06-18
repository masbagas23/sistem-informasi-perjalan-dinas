<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\BusinessTripApplication;
use Carbon\Carbon;
use Illuminate\Http\Response;

class ReportTripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = BusinessTripApplication::where('result', BusinessTripApplication::RESULT_DONE)->with(['customer:id,name']);

            if (isset(request()->order_column)){
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function($query){
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
                });
            }

            if(request()->month){
                $data = $data->whereMonth('start_date', Carbon::parse(request()->month));
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
}
