<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTripApplicationUserListResource extends JsonResource
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
            'application_id' => $this->application_id,
            'user_id' => $this->user_id,
            'name' => $this->user->first_name.' '.$this->user->last_name,
            'nip' => $this->user->nip,
            'job_position' => $this->user->jobPosition->name,
            'is_leader' => $this->is_leader,
            'updated_at' => $this->updated_at
        ];
    }
}
