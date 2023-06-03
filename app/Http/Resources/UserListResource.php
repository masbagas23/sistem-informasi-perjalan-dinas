<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
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
            'id' => $this->id,
            'email' => $this->email,
            'nip' => $this->nip,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'job_position_id' => $this->job_position_id,
            'job_position' => $this->jobPosition->name,
            'signature_url' => $this->signature_url,
            'ava_url' => $this->ava_url,
            'updated_at' => $this->updated_at
        ];
    }
}
