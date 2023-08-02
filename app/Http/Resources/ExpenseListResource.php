<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseListResource extends JsonResource
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
            'code' => $this->code,
            'application_id' => $this->application_id,
            'total_nominal' => $this->total_nominal,
            'down_payment' => $this->down_payment,
            'remaining_cost' => $this->remaining_cost > 0 ?? null,
            'reimburse_cost' => $this->remaining_cost < 0 ?? null,
            'is_reimburse' => $this->is_reimburse,
            'status' => $this->status,
            'status_reimburse' => $this->status_reimburse,
            'reimburse_path' => $this->reimburse_path,
            'updated_at' => $this->updated_at
        ];
    }
}
