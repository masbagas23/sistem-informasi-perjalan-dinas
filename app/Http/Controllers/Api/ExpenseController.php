<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\DB;
use Illuminate\Http\Request;
use App\Models\Expense as Model;
use Illuminate\Http\Response;
use App\Models\ExpenseDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (Auth::user()->jobPosition->role_id == 5) {
                $data = Model::with('application.customer');
            }else{
                $data = Model::with('application.customer')->whereHas('application', function($query){
                    $query->whereHas('users',function($user){
                        $user->where('user_id', Auth::id())->where('is_leader',1);
                    });
                });
            }
            if (isset(request()->order_column)){
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function($query){
                    $query->where('code', 'LIKE', '%' . request()->keyword . '%');
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

    public function loadList(Request $request)
    {
        try {
            $data = Model::query();
            if (request()->keyword != '') {
                $data = $data->where(function($query){
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            $data = $data->get();
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "application_id"=>"required",
            "details"=>"required"
        ]);

        DB::beginTransaction();
        try {
            // ~ Inisiasi variabel pembantu
            $details = $request->details;

            // A. Simpan Master Biaya Pengeluaran
            $master = Model::create([
                'code' => Model::generateCode(Carbon::now()),
                'application_id' => $request->application_id,
                'down_payment' => $request->down_payment,
                'status' => Model::STATUS_VALIDATION,
            ]);
            
            // B. Simpan Detail Biaya Pengeluaran
            foreach ($details as $detail) {
                // I. Konversi Base64 menjadi gambar dan simpan path nya
                $file_path = isset($detail['file']) ? base64ToImage($detail['file'], 'kwitansi') : $detail['file_path'];

                // II. Simpan Ke DB
                ExpenseDetail::create([
                    'expense_id' => $master->id,
                    'cost_category_id' => $detail['cost_category_id'],
                    'nominal' => $detail['nominal'],
                    'description'=> $detail['description'],
                    'file_path' => $file_path,
                    'status' => ExpenseDetail::STATUS_WAITING
                ]);
            }
            DB::commit();
            return response()->json([
                "status"=>"success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status'=>'error',
                'message'=>$th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Model::find($id);
            $data->load('details.costCategory');
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        try {
            // ~ Inisiasi variabel pembantu
            $master = Model::find($id);
            $details = $request->details;

            // A. Update Master Biaya Pengeluaran
            $master->update([
                'application_id' => $request->application_id,
                'down_payment' => $request->down_payment,
                'status' => Model::STATUS_VALIDATION,
            ]);
            
            // B. Update Detail Biaya Pengeluaran
            ExpenseDetail::where('expense_id', $master->id)->delete();
            foreach ($details as $detail) {
                // I. Konversi Base64 menjadi gambar dan simpan path nya
                $file_path = array_key_exists('file', $detail) ? base64ToImage($detail['file'], 'kwitansi') : $detail['file_path'];

                // II. Simpan Ke DB
                ExpenseDetail::withTrashed()->updateOrCreate([
                    'expense_id' => $master->id,
                    'cost_category_id' => $detail['cost_category_id'],
                    'nominal' => $detail['nominal'],
                    'file_path' => $file_path,
                ],[
                    'expense_id' => $master->id,
                    'cost_category_id' => $detail['cost_category_id'],
                    'nominal' => $detail['nominal'],
                    'description'=> $detail['description'],
                    'file_path' => $file_path,
                ])->restore();
            }
            return response()->json([
                "status"=>"success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>'error',
                'message'=>$th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Model::find($id)->delete();
        return response()->json([
            "status"=>"success"
        ], Response::HTTP_OK);
    }

    public function reimburse(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // ~ Inisiasi variabel pembantu
            $master = Model::find($id);
            $reimburse_path = isset($request->reimburse_file) ? base64ToImage($request->reimburse_file, 'reimburse') : null;

            $master->update([
                'reimburse_path' => $reimburse_path,
                'status_reimburse' => Model::STATUS_DONE,
            ]);
            DB::commit();
            return response()->json([
                "status"=>"success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status'=>'error',
                'message'=>$th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function validation(Request $request, $id)
    {
        
        DB::beginTransaction();
        try {
            // ~ Inisiasi variabel pembantu
            $master = Model::find($id);
            $details = $request->details;
            
            // A. Update Detail Biaya Pengeluaran
            foreach ($details as $detail) {
                // I. Simpan Ke DB
                ExpenseDetail::find($detail['id'])->update([
                    'status' => $detail['status']
                ]);
            }

            $total_nominal = ExpenseDetail::where('expense_id', $id)->where('status', ExpenseDetail::STATUS_VALID)->sum('nominal');
            $remaining_cost = $master->down_payment - $total_nominal;
            $master->update([
                'status' => Model::STATUS_DONE,
                'total_nominal' => $total_nominal,
                'remaining_cost' => $remaining_cost,
                'is_reimburse' => $remaining_cost < 0 ? 1 : 0,
                'status_reimburse' => $remaining_cost < 0 ? Model::STATUS_VALIDATION : null,
            ]);
            DB::commit();
            return response()->json([
                "status"=>"success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status'=>'error',
                'message'=>$th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function counter()
    {
        try {
            $data = Model::where('status', Model::STATUS_DONE);

            if (request()->filter_month) {
                $data = $data->whereHas('application', function($query){
                    $date = Carbon::parse(request()->filter_month);
                    $query->whereMonth('start_date', $date)->whereYear('start_date', $date);
                });
            }

            return response()->json([
                "status" => "success",
                "data" => [
                    "total" => $data->sum('total_nominal')
                ]
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
