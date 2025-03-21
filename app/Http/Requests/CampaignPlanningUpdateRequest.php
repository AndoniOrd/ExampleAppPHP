<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TrackingOptions;
use Illuminate\Validation\Rule;

class CampaignPlanningUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $trackingOptions = array_column(TrackingOptions::cases(), 'value');

        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'mailing_list_id' => 'nullable|exists:mailing_lists,id',
            'scheduled_time' => 'nullable|date',
            'time_zone' => 'nullable|timezone',
            'status_status_type' => 'nullable|in:draft,scheduled,processing,completed',
            'scheduled_by' => 'nullable|exists:users,id',
            'send_from_email' => 'nullable|email|max:255',
            'send_from_name' => 'nullable|string|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'tracking_options' => ['nullable', Rule::in($trackingOptions)],
        ];
    }
}