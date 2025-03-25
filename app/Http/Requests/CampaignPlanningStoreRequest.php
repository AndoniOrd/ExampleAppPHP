<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TrackingOptions;
use Illuminate\Validation\Rule;

class CampaignPlanningStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $trackingOptions = array_column(TrackingOptions::cases(), 'value');

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email_template_id' => 'required|exists:email_templates,id',
            'mailing_list_id' => 'required|exists:mailing_lists,id',
            'scheduled_time' => 'required|date',
            'time_zone' => ['required', 'timezone'],
            'status_type' => 'required|in:draft,scheduled,processing,completed',
            'scheduled_by' => 'required|exists:users,id',
            'send_from_email' => 'required|email|max:255',
            'send_from_name' => 'required|string|max:255',
            'reply_to_email' => 'required|email|max:255',
            'tracking_options' => ['required', Rule::in($trackingOptions)],
        ];
    }
}