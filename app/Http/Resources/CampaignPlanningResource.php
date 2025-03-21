<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignPlanningResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'description'         => $this->description,
            'email_template_id'   => $this->email_template_id,
            'mailing_list_id'     => $this->mailing_list_id,
            'scheduled_time'      => $this->scheduled_time->toDateTimeString(),
            'time_zone'           => $this->time_zone,
            'status_status_type'  => $this->status_status_type,
            'scheduled_by'        => $this->scheduled_by,
            'send_from_email'     => $this->send_from_email,
            'send_from_name'      => $this->send_from_name,
            'reply_to_email'      => $this->reply_to_email,
            'tracking_options'    => $this->tracking_options,
            'created_at'          => $this->created_at->toDateTimeString(),
            'updated_at'          => $this->updated_at->toDateTimeString(),
            // Computed field (e.g., "Marketing Team <marketing@example.com>")
            'send_from'           => "{$this->send_from_name} <{$this->send_from_email}>",
        ];
    }
}
