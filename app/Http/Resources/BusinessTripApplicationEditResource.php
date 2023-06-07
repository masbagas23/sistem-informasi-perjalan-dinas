<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTripApplicationEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // 'id'=>$this->id,
            'code' => $this->code,
            'code_letter' => $this->code_letter,
            'customer_id' => $this->customer_id,
            'customer' => $this->customer->name,
            'job_category_id' => $this->job_category_id,
            'job_category' => $this->jobCategory->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'note' => $this->note,
            'requested_by' => $this->requested_by,
            'requester' => $this->requester->first_name.' '.$this->requester->last_name,
            'status' => $this->status,
            'result' => $this->result,
            'total_day' => $this->total_day,
            'description' => $this->description,
            'users' => BusinessTripApplicationUserListResource::collection($this->users),
            'targets' => $this->targets,
            'updated_at' => $this->updated_at
        ];
    }
}
