<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserListResource;
use App\Models\BusinessTripApplication;
use Illuminate\Http\Request;
use App\Models\User as Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Model::with(['jobPosition:id,name']);

            if (isset(request()->order_column)){
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function($query){
                    $query->where('first_name', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('nip', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . request()->keyword . '%')
                    ->orWhere('middle_name', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            $data = $data->paginate(request()->per_page);
            return UserListResource::collection($data);
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
            $data = Model::with(['jobPosition:id,name']);
            if (request()->keyword != '') {
                $data = $data->where(function($query){
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            if(request()->filter_job_position){
                $data = $data->whereDoesntHave('applications.application', function($query){
                    $query->where('status', '!=', BusinessTripApplication::STATUS_REJECT)->where(function($date){
                        $date->whereBetween('start_date', [request()->start_date, request()->end_date])->orWhereBetween('start_date', [request()->start_date, request()->end_date]);
                    });
                });
                // dd($data->get());
                switch (request()->filter_job_position) {
                    case 'participant':
                        $data = $data->whereHas('jobPosition', function($q){
                            $q->where('id', '>', 2)->where('id', '<', 5);
                        });
                        break;
                    default:
                        $data = $data->whereHas('jobPosition', function($q){
                            $q->where('id', request()->filter_job_position);
                        });
                        break;
                }
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
            "nip"=>"required|min:4|max:10|unique:users,nip",
            "job_position_id"=>"required",
            "first_name"=>"required",
            "last_name"=>"required",
            "email"=>"required",
            "phone_number"=>"required",
            "gender"=>"required",
            "bank_number"=>"required",
            // "signature"=>"required|mimes:jpg,jpeg,png,webp|max:2048",
            // "file"=>"mimes:jpg,jpeg,png,webp|max:2048"
        ]);

        try {
            $ava_url = '/images/ava.webp';
            // Simpan Foto
            if($request->hasFile('file')){
                $ava_url = Model::savePhoto($request->file('file'), 'avatar');
            }
            // Simpan tanda tangan
            if($request->hasFile('signature')){
                $signature_url = Model::savePhoto($request->file('signature'), 'signature');
            }
            //
            $request->request->add(['avatar_url' => $ava_url]);
            $request->request->add(['signature_url' => $signature_url ?? null]);
            $request->request->add(['password' => Hash::make('password')]);
            Model::create($request->all());
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
        $request->validate([
            "nip"=>"required|min:4|max:10|unique:users,nip,".$id,
            "job_position_id"=>"required",
            "first_name"=>"required",
            "last_name"=>"required",
            "email"=>"required",
            "phone_number"=>"required",
            "gender"=>"required",
            "bank_number"=>"required",
            // "signature"=>"mimes:jpg,jpeg,png,webp|max:2048",
            // "file"=>"mimes:jpg,jpeg,png,webp|max:2048"
        ]);

        try {
            // Simpan Foto
            if($request->hasFile('file')){
                $ava_url = Model::savePhoto($request->file('file'), 'avatar');
                $request->request->add(['avatar_url' => $ava_url]);
            }
            // Simpan tanda tangan
            if($request->hasFile('signature')){
                $signature_url = Model::savePhoto($request->file('signature'), 'signature');
                $request->request->add(['signature_url' => $signature_url]);
            }
            Model::find($id)->update($request->all());
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
}
