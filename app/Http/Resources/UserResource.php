<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'roles' => $this->roles->pluck('name'),
            'permissions' => $this->permissions->pluck('name'),
            'account_status' => $this->account_status,
            'creation_date' => $this->creation_date,
            'company_name' => $this->company_name,
            'vat_tax_id' => $this->vat_tax_id,
            'company_address' => $this->company_address,
            'industry' => $this->industry,
            'company_size' => $this->company_size,
            'website' => $this->website,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}