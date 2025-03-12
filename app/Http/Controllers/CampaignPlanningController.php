<?php

namespace App\Http\Controllers;

use App\Models\CampaignPlanning;
use App\Enums\TrackingOptions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignPlanningController extends Controller
{
    /**
     * Listar todas las planificaciones de campañas
     */
    public function index()
    {
        $campaignPlannings = CampaignPlanning::all();
        return response()->json(['data' => $campaignPlannings]);
    }

    /**
     * Crear una nueva planificación de campaña
     */
    public function store(Request $request)
    {
        $trackingOptions = array_column(TrackingOptions::cases(), 'value');

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email_template_id' => 'required|exists:email_templates,id',
            'mailing_list_id' => 'required|exists:mailing_lists,id',
            'scheduled_time' => 'required|date',
            'time_zone' => ['required', 'timezone'],
            'status_status_type' => 'required|in:draft,scheduled,processing,completed',
            'scheduled_by' => 'required|exists:users,id',
            'send_from_email' => 'required|email|max:255',
            'send_from_name' => 'required|string|max:255',
            'reply_to_email' => 'required|email|max:255',
            'tracking_options' => ['required', Rule::in($trackingOptions)],
        ];

        $data = $request->validate($rules);

        $campaignPlanning = CampaignPlanning::create($data);

        return response()->json([
            'data' => [
                'id' => $campaignPlanning->id,
                'name' => $campaignPlanning->name,
                'status' => $campaignPlanning->status_status_type,
                'scheduled_time' => $campaignPlanning->scheduled_time->toDateTimeString(),
            ]
        ], 201);
    }

    /**
     * Mostrar una planificación específica
     */
    public function show(int $id)
    {
        $campaignPlanning = CampaignPlanning::findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $campaignPlanning->id,
                'name' => $campaignPlanning->name,
                'status' => $campaignPlanning->status_status_type,
                'scheduled_time' => $campaignPlanning->scheduled_time->toDateTimeString(),
                'send_from' => "{$campaignPlanning->send_from_name} <{$campaignPlanning->send_from_email}>",
            ]
        ]);
    }

    /**
     * Actualizar una planificación existente
     */
    public function update(Request $request, int $id)
    {
        $campaignPlanning = CampaignPlanning::findOrFail($id);
        $trackingOptions = array_column(TrackingOptions::cases(), 'value');

        $rules = [
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

        $data = $request->validate($rules);
        $campaignPlanning->update($data);
        $campaignPlanning->refresh();

        return response()->json([
            'data' => [
                'id' => $campaignPlanning->id,
                'name' => $campaignPlanning->name,
                'status' => $campaignPlanning->status_status_type,
                'scheduled_time' => $campaignPlanning->scheduled_time->toDateTimeString(),
            ]
        ]);
    }

    /**
     * Eliminar una planificación
     */
    public function destroy(int $id)
    {
        $campaignPlanning = CampaignPlanning::findOrFail($id);
        $campaignPlanning->delete();
        return response()->noContent();
    }
}