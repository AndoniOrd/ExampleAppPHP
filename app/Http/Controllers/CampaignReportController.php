<?php

namespace App\Http\Controllers;

use App\Models\CampaignPlanning;
use App\Models\CampaignReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CampaignReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = CampaignReport::with('campaignPlanning')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($reports);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaignPlannings = CampaignPlanning::all();
        return response()->json(['campaignPlannings' => $campaignPlannings]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Calculate click-to-open rate
        $validated['click_to_open_rate'] = $this->calculateClickToOpenRate(
            $validated['opens_unique'] ?? 0,
            $validated['clicks_unique'] ?? 0
        );

        // Encode JSON fields
        $validated['device_statistics'] = json_encode($validated['device_statistics']);
        $validated['geographical_data'] = json_encode($validated['geographical_data']);
        $validated['time_based_metrics'] = json_encode($validated['time_based_metrics']);

        $report = CampaignReport::create($validated);

        return response()->json(['message' => 'Campaign report created successfully.', 'data' => $report], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CampaignReport $campaignReport)
    {
        $campaignReport->load('campaignPlanning');
        return response()->json($campaignReport);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CampaignReport $campaignReport)
    {
        $campaignPlannings = CampaignPlanning::all();
        return response()->json([
            'campaignReport' => $campaignReport,
            'campaignPlannings' => $campaignPlannings,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CampaignReport $campaignReport)
    {
        $validator = Validator::make($request->all(), $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Calculate click-to-open rate
        $validated['click_to_open_rate'] = $this->calculateClickToOpenRate(
            $validated['opens_unique'] ?? 0,
            $validated['clicks_unique'] ?? 0
        );

        // Encode JSON fields
        $validated['device_statistics'] = json_encode($validated['device_statistics']);
        $validated['geographical_data'] = json_encode($validated['geographical_data']);
        $validated['time_based_metrics'] = json_encode($validated['time_based_metrics']);

        $campaignReport->update($validated);

        return response()->json(['message' => 'Campaign report updated successfully.', 'data' => $campaignReport]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CampaignReport $campaignReport)
    {
        $campaignReport->delete();
        return response()->json(['message' => 'Campaign report deleted successfully.']);
    }

    /**
     * Validation rules for campaign reports
     */
    protected function validationRules()
    {
        return [
            'campaign_planning_id' => 'required|exists:campaign_plannings,id',
            'total_recipients' => 'required|integer|min:1',
            'successful_deliveries' => 'required|integer|min:0|lte:total_recipients',
            'hard_bounces' => 'required|integer|min:0',
            'soft_bounces' => 'required|integer|min:0',
            'opens_count' => 'required|integer|min:0',
            'opens_unique' => 'required|integer|min:0|lte:opens_count',
            'clicks_count' => 'required|integer|min:0',
            'clicks_unique' => 'required|integer|min:0|lte:clicks_count',
            'unsubscribes' => 'required|integer|min:0',
            'spam_complaints' => 'required|integer|min:0',
            'device_statistics' => 'required|array',
            'device_statistics.desktop' => 'required|integer|min:0',
            'device_statistics.mobile' => 'required|integer|min:0',
            'device_statistics.tablet' => 'required|integer|min:0',
            'device_statistics.unknown' => 'required|integer|min:0',
            'geographical_data' => 'required|array',
            'time_based_metrics' => 'required|array',
            'time_based_metrics.morning' => 'required|integer|min:0',
            'time_based_metrics.afternoon' => 'required|integer|min:0',
            'time_based_metrics.evening' => 'required|integer|min:0',
            'engagement_score' => 'required|numeric|between:0,100',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function validationMessages()
    {
        return [
            'successful_deliveries.lte' => 'Successful deliveries cannot exceed total recipients.',
            'opens_unique.lte' => 'Unique opens cannot exceed total opens.',
            'clicks_unique.lte' => 'Unique clicks cannot exceed total clicks.',
            'device_statistics.*.required' => 'All device statistics fields are required',
            'time_based_metrics.*.required' => 'All time-based metrics fields are required',
        ];
    }

    /**
     * Calculate click-to-open rate
     */
    protected function calculateClickToOpenRate($opensUnique, $clicksUnique)
    {
        if ($opensUnique > 0) {
            return round(($clicksUnique / $opensUnique) * 100, 2);
        }
        return 0;
    }
}