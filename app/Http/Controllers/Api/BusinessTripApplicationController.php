<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessTripApplicationEditResource;
use App\Http\Resources\DataCollection;
use Illuminate\Http\Request;
use App\Models\BusinessTripApplication as Model;
use App\Models\BusinessTripApplicationTarget;
use App\Models\BusinessTripApplicationUser;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\DB;
use App\Models\User;
use App\Plugins\NotificationAPI;
use Illuminate\Support\Facades\Auth;
use PDF;

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
            if (Auth::user()->jobPosition->role_id == 2) {
                $data = Model::with(['customer', 'requester.jobPosition', 'jobCategory:id,name']);
            } elseif (Auth::user()->jobPosition->role_id == 3) {
                $data = Model::with(['customer', 'requester.jobPosition', 'jobCategory:id,name'])->where(function ($query) {
                    $query->where('requested_by', Auth::id());
                });
            } elseif (Auth::user()->jobPosition->role_id == 4) {
                $data = Model::with(['customer', 'requester.jobPosition', 'jobCategory:id,name'])->whereHas('users', function ($query) {
                    $query->where('user_id', Auth::id())->where('is_leader', 1);
                });
            } else {
                $data = Model::with(['customer', 'requester.jobPosition', 'jobCategory:id,name'])->where(function ($query) {
                    $query->where('requested_by', Auth::id())
                        ->orWhereHas('users', function ($user) {
                            $user->where('user_id', Auth::id())->where('is_leader', 1);
                        });
                });
            }

            if (isset(request()->order_column)) {
                $data = $data->orderBy(request()->order_column, (request()->order_direction == 'true' ? 'DESC' : 'ASC'));
            }

            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('code', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            if (isset(request()->month)) {
                $month = Carbon::parse(request()->month);
                $data = $data->whereMonth('start_date', $month)->whereYear('start_date', $month);
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
            $data = Model::with(['customer:id,name']);
            if (request()->keyword != '') {
                $data = $data->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request()->keyword . '%');
                });
            }
            if (request()->page) {
                switch (request()->page) {
                    case 'vehicle_loan':
                        $data = $data->whereHas('users', function ($query) {
                            $query->where('user_id', Auth::id())->where('is_leader', 1);
                        })->whereDoesntHave('vehicleLoan');
                        break;
                    case 'down_payment':
                        $data = $data->whereHas('users', function ($query) {
                            $query->where('user_id', Auth::id())->where('is_leader', 1);
                        })->whereDoesntHave('downPayment');
                        break;
                    default:
                        $data = $data->whereHas('users', function ($query) {
                            $query->where('user_id', Auth::id())->where('is_leader', 1);
                        });
                        break;
                }
            }
            if (request()->status) {
                switch (request()->status) {
                    case 'approved':
                        $data = $data->where('status', Model::STATUS_APPROVE);
                        break;
                    default:
                        # code...
                        break;
                }
            }

            if (request()->result) {
                switch (request()->result) {
                    case 'waiting':
                        $data = $data->where('result', Model::RESULT_ON_PROGRESS);
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
        Validator::extend('business_trip_validator', function ($attr, $value, $parameters) {
            switch ($parameters[0]) {
                case 'users':
                    if (count($value) > 0){
                        if($value[0]['user_id'] > 0)return true;
                    };
                    return false;
                    break;
                case 'targets':
                    if (count($value) > 0){
                        if($value[0]['name'] != null)return true;
                    };
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
            $requester = auth()->user();
            $code = Model::generateCode(Carbon::now());
            // $pic = '';
            // $customer = Customer::select('name')->where('id', $request->customer_id)->first();
            $master = Model::create([
                'code' => $code,
                'customer_id' => $request->customer_id,
                'job_category_id' => $request->job_category_id,
                'description' => $request->description,
                'start_date' => Carbon::parse($request->start_date)->format('Y-m-d'),
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),
                'total_day' => $request->total_day,
                'requested_by' => $requester->id,
                'status' => Model::STATUS_WAITING
            ]);

            if (count($users) > 0) {
                foreach ($users as $index => $user) {
                    if ($index == 0) $pic = User::where('id', $user['user_id'])->first();
                    BusinessTripApplicationUser::create([
                        'application_id' => $master->id,
                        'user_id' => $user['user_id'],
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

            // $presindents = User::select('email')->whereHas('jobPosition.role', fn ($q) => $q->where('id', 2))->get();
            // if (count($presindents) > 0) {
            //     foreach ($presindents as $president) {
            //         $notificationapi = new NotificationAPI(env('NOTIF_CLIENT_ID', '585rb2fl4je58k1ut46oviauc8'), env('NOTIF_CLIENT_SECRET', 't88ogvufanbtu4j3a83vn6b524og570a4rjni8n8ah3cnr2ai2t'));
            //         $notificationapi->send([
            //             "notificationId" => "pengajuan_perjalanan_dinas",
            //             "user" => [
            //                 "id" => $president['email'],
            //                 "email" => "hasea23@gmail.com",   # required for email notifications
            //             ],
            //             "mergeTags" => [
            //                 "host" => env('APP_URL', 'https://perjalanandinas.bagasraga.my.id'),
            //                 "code" => $code,
            //                 "avatar_url" => $requester->avatar_url,
            //                 "pemohon" => $requester->first_name . ' ' . $requester->last_name,
            //                 "tujuan" => $customer->name,
            //                 "tanggal" => $request->total_day > 1 ? $master->start_date . ' - ' . $master->end_date : $master->start_date,
            //                 "koordinator" => $pic->first_name .' '.$pic->last_name
            //             ]
            //         ]);
            //     }
            // }

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
            $data->load(['users.user.jobPosition', 'targets', 'customer:id,name', 'jobCategory:id,name', 'requester:id,first_name,last_name']);
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
            $master = Model::find($id);
            $master->update([
                'customer_id' => $request->customer_id,
                'job_category_id' => $request->job_category_id,
                'description' => $request->description,
                'start_date' => Carbon::parse($request->start_date)->format('Y-m-d'),
                'end_date' => Carbon::parse($request->end_date)->format('Y-m-d'),
                'total_day' => $request->total_day,
                'status' => Model::STATUS_WAITING
            ]);

            if (count($users) > 0) {
                BusinessTripApplicationUser::where('application_id', $master->id)->delete();
                foreach ($users as $user) {
                    BusinessTripApplicationUser::withTrashed()->updateOrCreate([
                        'application_id' => $master->id,
                        'user_id' => $user['user_id'],
                        'is_leader' => $user['is_leader'],
                    ], [
                        'application_id' => $master->id,
                        'user_id' => $user['user_id'],
                        'is_leader' => $user['is_leader'],
                    ])->restore();
                }
            }
            if (count($targets) > 0) {
                BusinessTripApplicationTarget::where('application_id', $master->id)->forceDelete();
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
            "note" => "required_if:status,4",
        ]);

        try {
            $master = Model::find($id);
            $code_letter = $request->status == Model::STATUS_APPROVE ? Model::generateCodeLetter(Carbon::parse($master->start_date)) : null;
            $master->update([
                "code_letter" => $code_letter,
                "note" => $request->status == Model::STATUS_APPROVE ? null : $request->note,
                "approved_by" => auth()->user()->id,
                "status" => $request->status == Model::STATUS_APPROVE ? Model::STATUS_APPROVE : Model::STATUS_REJECT,
                "result" => $request->status == Model::STATUS_APPROVE ? Model::RESULT_ON_PROGRESS : Model::RESULT_PENDING
            ]);

            // if($request->status == Model::STATUS_APPROVE){
            //     $pic = BusinessTripApplicationUser::with('user:id,first_name,email')->select('user_id')->where('is_leader', 1)->where('application_id', $id)->first();
            //     $notificationapi = new NotificationAPI(env('NOTIF_CLIENT_ID', '585rb2fl4je58k1ut46oviauc8'), env('NOTIF_CLIENT_SECRET', 't88ogvufanbtu4j3a83vn6b524og570a4rjni8n8ah3cnr2ai2t'));
            //     $notificationapi->send([
            //         "notificationId" => "persetujuan_perjalanan_dinas",
            //         "user" => [
            //             "id" => $pic->user->email,
            //             "email" => "hasea23@gmail.com",   # required for email notifications
            //         ],
            //         "mergeTags" => [
            //             "host" => env('APP_URL', 'https://perjalanandinas.bagasraga.my.id'),
            //             "code" => $master->code,
            //             "pic" => $pic->customer->first_name,
            //             "pekerjaan" => $master->jobCategory->name,
            //             "pelanggan" => $master->customer->name
            //         ]
            //     ]);
            // }
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

    public function cancel(Request $request, $id)
    {
        $request->validate([
            "note" => "required",
        ]);

        try {
            Model::find($id)->update([
                "note" => $request->note,
                "status" => Model::STATUS_CANCEL
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

    public function report(Request $request, $id)
    {
        $request->validate([
            "targets" => "required",
        ]);

        try {
            Model::find($id)->update(['result' => Model::RESULT_DONE]);
            foreach ($request->targets as $target) {
                $file_path = isset($target['file']) ? base64ToImage($target['file'], 'target') : ($target['file_path'] ?? null);

                $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);
                $duration = $end_date->diffInDays($start_date);
                BusinessTripApplicationTarget::updateOrCreate([
                    'application_id' => $id,
                    'id' => $target['id']
                ], [
                    'application_id' => $id,
                    'start_date' => $start_date->format('Y-m-d'),
                    'end_date' => $end_date->format('Y-m-d'),
                    'duration' => $duration,
                    'status' => $request->status,
                    'file_path' => $file_path,
                    'reason' => $request->reason,
                ]);
            }
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

    public function letter($id)
    {
        $business_trip = Model::find($id);
        $business_trip->load(['users.user.jobPosition', 'targets', 'customer', 'jobCategory:id,name', 'approver.jobPosition', 'requester:id,first_name,last_name']);
        $pdf = PDF::loadView('pdf.sppd', compact('business_trip'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }

    public function counter()
    {
        try {
            $data = Model::where('status', Model::STATUS_APPROVE);

            if (request()->filter_month) {
                $date = Carbon::parse(request()->filter_month);
                $data = $data->whereMonth('start_date', $date)->whereYear('start_date', $date);
            }

            return response()->json([
                "status" => "success",
                "data" => [
                    "total" => $data->count()
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
