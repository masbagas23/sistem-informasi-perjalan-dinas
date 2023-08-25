<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\BusinessTripApplication;
use Illuminate\Http\Request;
use App\Models\Vehicle as Model;
use App\Models\VehicleLoan;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Model::query();

            if (isset(request()->order_column)) {
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%')
                        ->orWhere('number_plate_license', 'LIKE', '%' . request()->keyword . '%');
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

    public function loadList()
    {
        try {
            $data = Model::query();
            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%')
                        ->orWhere('number_plate_license', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            
            if (request()->category > 0) {
                $data = $data->where('category', request()->category);
            }
            if (request()->page) {
                switch (request()->page) {
                    case 'vehicle_loan':
                        $data = $data->where('condition', 1)->whereDoesntHave('loans', function ($query) {
                            $query->whereIn('status', [VehicleLoan::STATUS_APPROVE, VehicleLoan::STATUS_WAITING])->where(function ($date) {
                                $start_date = Carbon::parse(request()->start_date);
                                $end_date = Carbon::parse(request()->end_date);
                                $date->whereBetween('start_date', [$start_date, $end_date])->orWhereBetween('end_date', [$start_date, $end_date]);
                            });
                        });
                        break;
                    default:
                        # code...
                        break;
                }
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
            "name" => "required",
            "category" => "required",
            "condition" => "required",
            "number_plate_license" => "required|min:6",
        ]);

        try {
            Model::create($request->all());
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
            "name" => "required",
            "category" => "required",
            "condition" => "required",
            "number_plate_license" => "required|min:6",
        ]);

        try {
            Model::find($id)->update($request->all());
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
}
