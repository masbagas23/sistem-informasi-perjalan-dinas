<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessTripApplicationEditResource;
use App\Http\Resources\DataCollection;
use Illuminate\Http\Request;
use App\Models\BusinessTripApplication as Model;
use App\Models\BusinessTripApplicationTarget;
use App\Models\BusinessTripApplicationUser;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\DB;

class BusinessTripApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Model::with(['requester', 'jobCategory:id,name']);

            if (isset(request()->order_column)) {
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
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
        Validator::extend('business_trip_validator', function ($attr, $value, $parameters) {
            switch ($parameters[0]) {
                case 'users':
                    if (count($value) > 0) return true;
                    return false;
                    break;
                case 'targets':
                    if (count($value) > 0) return true;
                    return false;
                    break;
                default:
                    return true;
                    break;
            }
        }, "This field is required");

        $request->validate([
            "customer_id" => "required",
            "job_category_id" => "required",
            "description" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "users" => "required|business_trip_validator:users",
            "targets" => "required|business_trip_validator:targets"
        ]);
        DB::beginTransaction();
        try {
            $users = $request->users;
            $targets = $request->targets;
            $master = Model::create([
                'code' => Model::generateCode(Carbon::now()),
                'customer_id' => $request->customer_id,
                'job_category_id' => $request->job_category_id,
                'description' => $request->description,
                'start_date' => Carbon::parse($request->start_date)->format('Y-m-d'),
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),
                'total_day' => $request->total_day,
                'requested_by' => auth()->user()->id,
                'status' => Model::STATUS_WAITING
            ]);

            if (count($users) > 0) {
                foreach ($users as $user) {
                    BusinessTripApplicationUser::create([
                        'application_id' => $master->id,
                        'user_id' => $user['id'],
                        'is_leader' => $user['is_leader'],
                    ]);
                }
            }
            if (count($targets) > 0) {
                foreach ($targets as $target) {
                    BusinessTripApplicationTarget::create([
                        'application_id' => $master->id,
                        'name' => $target['name'],
                        'description' => $target['description'],
                        'status' => BusinessTripApplicationTarget::STATUS_WAITING,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                "status" => "success"
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
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
            $data->load(['users.user', 'targets']);
            $data = new BusinessTripApplicationEditResource($data);
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
            "name" => "required"
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
