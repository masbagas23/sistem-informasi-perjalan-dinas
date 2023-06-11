<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use Illuminate\Http\Request;
use App\Models\VehicleLoan as Model;
use Carbon\Carbon;
use Illuminate\Http\Response;

class VehicleLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Model::with(['application:id,code', 'vehicle', 'requester:id,first_name,middle_name,last_name']);

            if (isset(request()->order_column)) {
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('code', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            $data = $data->paginate(request()->per_page);
            return new DataCollection($data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function loadList(Request $request)
    {
        try {
            $data = Model::query();
            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
                });
            }

            $data = $data->get();
            return response()->json([
                "status" => "success",
                "data" => $data
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
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
            "application_id" => "required",
            "vehicle_id" => "required"
        ]);

        try {
            Model::create([
                'code' => Model::generateCode(Carbon::now()),
                'application_id' => $request->application_id,
                'vehicle_id' => $request->vehicle_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_day' => $request->total_day,
                'status' => Model::STATUS_WAITING,
                'requested_by' => auth()->user()->id,
                'note' => $request->note,
            ]);
            return response()->json([
                "status" => "success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
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
            $data->load(['vehicle','application','requester']);
            return response()->json([
                "status" => "success",
                "data" => $data
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
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
        $request->validate([
            "application_id" => "required",
            "vehicle_id" => "required"
        ]);

        try {
            Model::find($id)->update([
                'application_id' => $request->application_id,
                'vehicle_id' => $request->vehicle_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_day' => $request->total_day,
                'note' => $request->note,
            ]);
            return response()->json([
                "status" => "success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
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
            "status" => "success"
        ], Response::HTTP_OK);
    }

    public function approval(Request $request, $id)
    {
        $request->validate([
            "note"=> "required_unless:status,2",
        ]);

        try {
            Model::find($id)->update([
                "status" => $request->status == Model::STATUS_APPROVE ? Model::STATUS_APPROVE : Model::STATUS_REJECT,
                "note" => $request->note,
                "approved_by" => auth()->user()->id,
            ]);
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

    public function cancel(Request $request, $id)
    {
        $request->validate([
            "note"=>"required",
        ]);

        try {
            Model::find($id)->update([
                "note" => $request->note,
                "status" => Model::STATUS_CANCEL
            ]);
            dd("OK");
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
}
