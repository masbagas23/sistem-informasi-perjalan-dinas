<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\BusinessTripApplication as Model;
use Carbon\Carbon;
use Illuminate\Http\Response;
use PDF;
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
            $data = Model::where('result', Model::RESULT_DONE)->with(['customer:id,name', 'jobCategory:id,name', 'users'=>function($query){$query->where('is_leader', 1);}, 'users.user:id,first_name,last_name'])->withSum(['expenses'=> fn($query) => $query->where('status', 2)], 'total_nominal');

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

    public function print()
    {
        $data = Model::where('result', Model::RESULT_DONE)->with(['customer:id,name', 'jobCategory:id,name', 'users'=>function($query){$query->where('is_leader', 1);}, 'users.user:id,first_name,last_name'])->withSum(['expenses'=> fn($query) => $query->where('status', 2)], 'total_nominal');
        $month = '';
        $year = '';
        if(request()->month){
            $date = Carbon::parse(request()->month);
            $month = $date->translatedFormat('F');
            $year = $date->format('Y');
            $data = $data->whereMonth('start_date', $date)->whereYear('start_date', $date);
        }
        $data = $data->get();
        $total = $data->sum('expenses_sum_total_nominal');
        $pdf = PDF::loadView('pdf.report-trips', compact(['data','month','year','total']))
            ->setPaper('a4', 'landscape');

        return $pdf->stream();
    }
}
