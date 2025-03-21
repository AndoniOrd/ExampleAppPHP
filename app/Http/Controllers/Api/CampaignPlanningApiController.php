<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignPlanning;
use App\Http\Requests\CampaignPlanningStoreRequest;
use App\Http\Requests\CampaignPlanningUpdateRequest;
use App\Http\Resources\CampaignPlanningIndexResource;
use App\Http\Resources\CampaignPlanningResource;

/**
 * @OA\Tag(
 *     name="Campaign Planning",
 *     description="API Endpoints for Campaign Planning Management"
 * )
 */

 /**
 * @OA\Schema(
 *     schema="CampaignPlanning",
 *     title="Campaign Planning",
 *     description="Campaign Planning model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Campaign for spring products"),
 *     @OA\Property(property="email_template_id", type="integer", example=5),
 *     @OA\Property(property="mailing_list_id", type="integer", example=3),
 *     @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *     @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *     @OA\Property(property="status_status_type", type="string", enum={"draft", "scheduled", "processing", "completed"}, example="scheduled"),
 *     @OA\Property(property="scheduled_by", type="integer", example=10),
 *     @OA\Property(property="send_from_email", type="string", format="email", example="marketing@example.com"),
 *     @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *     @OA\Property(property="reply_to_email", type="string", format="email", example="support@example.com"),
 *     @OA\Property(property="tracking_options", type="string", example="open_click"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-10 15:30:00")
 * )
 */

/**
 * @OA\Schema(
 *     schema="CampaignPlanningRequest",
 *     title="Campaign Planning Request",
 *     description="Campaign Planning request body data",
 *     required={"name", "email_template_id", "mailing_list_id", "scheduled_time", "time_zone", "status_status_type", "scheduled_by", "send_from_email", "send_from_name", "reply_to_email", "tracking_options"},
 *     @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Campaign for spring products"),
 *     @OA\Property(property="email_template_id", type="integer", example=5),
 *     @OA\Property(property="mailing_list_id", type="integer", example=3),
 *     @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *     @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *     @OA\Property(property="status_status_type", type="string", enum={"draft", "scheduled", "processing", "completed"}, example="scheduled"),
 *     @OA\Property(property="scheduled_by", type="integer", example=10),
 *     @OA\Property(property="send_from_email", type="string", format="email", example="marketing@example.com"),
 *     @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *     @OA\Property(property="reply_to_email", type="string", format="email", example="support@example.com"),
 *     @OA\Property(property="tracking_options", type="string", example="open_click")
 * )
 */

/**
 * @OA\Schema(
 *     schema="CampaignPlanningUpdateRequest",
 *     title="Campaign Planning Update Request",
 *     description="Campaign Planning update request body data",
 *     @OA\Property(property="name", type="string", example="Updated Spring Campaign"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Updated campaign description"),
 *     @OA\Property(property="email_template_id", type="integer", example=5),
 *     @OA\Property(property="mailing_list_id", type="integer", example=3),
 *     @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-20 10:00:00"),
 *     @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *     @OA\Property(property="status_status_type", type="string", enum={"draft", "scheduled", "processing", "completed"}, example="scheduled"),
 *     @OA\Property(property="scheduled_by", type="integer", example=10),
 *     @OA\Property(property="send_from_email", type="string", format="email", example="marketing@example.com"),
 *     @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *     @OA\Property(property="reply_to_email", type="string", format="email", example="support@example.com"),
 *     @OA\Property(property="tracking_options", type="string", example="open_click")
 * )
 */

/**
 * @OA\Schema(
 *     schema="CampaignPlanningResponse",
 *     title="Campaign Planning Response",
 *     description="Campaign Planning response data",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *         @OA\Property(property="status", type="string", example="scheduled"),
 *         @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00")
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="CampaignPlanningDetails",
 *     title="Campaign Planning Details",
 *     description="Campaign Planning detailed response data",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *         @OA\Property(property="status", type="string", example="scheduled"),
 *         @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *         @OA\Property(property="send_from", type="string", example="Marketing Team <marketing@example.com>")
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="Campaign Planning",
 *     description="API Endpoints for Campaign Planning Management"
 * )
 */
class CampaignPlanningApiController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/campaign-plannings",
 *     summary="List all campaign plannings",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *                     @OA\Property(property="description", type="string", nullable=true, example="Campaign for spring products"),
 *                     @OA\Property(property="email_template_id", type="integer", example=5),
 *                     @OA\Property(property="mailing_list_id", type="integer", example=3),
 *                     @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *                     @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *                     @OA\Property(property="status_status_type", type="string", example="scheduled"),
 *                     @OA\Property(property="scheduled_by", type="integer", example=10),
 *                     @OA\Property(property="send_from_email", type="string", example="marketing@example.com"),
 *                     @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *                     @OA\Property(property="reply_to_email", type="string", example="support@example.com"),
 *                     @OA\Property(property="tracking_options", type="string", example="open_click"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-01 12:00:00"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-10 15:30:00")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
public function index()
{
    $campaignPlannings = CampaignPlanning::orderBy('id', 'asc')->get();

    return CampaignPlanningIndexResource::collection($campaignPlannings);
}
 /**
 * @OA\Post(
 *     path="/api/campaign-plannings",
 *     summary="Create new campaign planning",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"name", "email_template_id", "mailing_list_id", "scheduled_time", "time_zone", "status_status_type", "scheduled_by", "send_from_email", "send_from_name", "reply_to_email", "tracking_options"},
 *             @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *             @OA\Property(property="description", type="string", nullable=true, example="Campaign for spring products"),
 *             @OA\Property(property="email_template_id", type="integer", example=5),
 *             @OA\Property(property="mailing_list_id", type="integer", example=3),
 *             @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *             @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *             @OA\Property(property="status_status_type", type="string", enum={"draft", "scheduled", "processing", "completed"}, example="scheduled"),
 *             @OA\Property(property="scheduled_by", type="integer", example=10),
 *             @OA\Property(property="send_from_email", type="string", format="email", example="marketing@example.com"),
 *             @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *             @OA\Property(property="reply_to_email", type="string", format="email", example="support@example.com"),
 *             @OA\Property(property="tracking_options", type="string", example="open_click")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *                 @OA\Property(property="status", type="string", example="scheduled"),
 *                 @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00")
 *             )
 *         )
 *     )
 * )
 */
public function store(CampaignPlanningStoreRequest $request)
{
    $data = $request->validated();
    $campaignPlanning = CampaignPlanning::create($data);

    return (new CampaignPlanningResource($campaignPlanning))
            ->response()
            ->setStatusCode(201);
}



    /**
 * @OA\Get(
 *     path="/api/campaign-plannings/{id}",
 *     summary="Get specific campaign planning",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Campaign Planning ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *                 @OA\Property(property="status", type="string", example="scheduled"),
 *                 @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00"),
 *                 @OA\Property(property="send_from", type="string", example="Marketing Team <marketing@example.com>")
 *             )
 *         )
 *     )
 * )
 */
public function show(int $id)
{
    $campaignPlanning = CampaignPlanning::findOrFail($id);

    return new CampaignPlanningResource($campaignPlanning);
}
   /**
 * @OA\Put(
 *     path="/api/campaign-plannings/{id}",
 *     summary="Update campaign planning",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Campaign Planning ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Updated Spring Campaign"),
 *             @OA\Property(property="description", type="string", nullable=true, example="Updated campaign description"),
 *             @OA\Property(property="email_template_id", type="integer", example=5),
 *             @OA\Property(property="mailing_list_id", type="integer", example=3),
 *             @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-20 10:00:00"),
 *             @OA\Property(property="time_zone", type="string", example="America/New_York"),
 *             @OA\Property(property="status_status_type", type="string", enum={"draft", "scheduled", "processing", "completed"}, example="scheduled"),
 *             @OA\Property(property="scheduled_by", type="integer", example=10),
 *             @OA\Property(property="send_from_email", type="string", format="email", example="marketing@example.com"),
 *             @OA\Property(property="send_from_name", type="string", example="Marketing Team"),
 *             @OA\Property(property="reply_to_email", type="string", format="email", example="support@example.com"),
 *             @OA\Property(property="tracking_options", type="string", example="open_click")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Spring Marketing Campaign"),
 *                 @OA\Property(property="status", type="string", example="scheduled"),
 *                 @OA\Property(property="scheduled_time", type="string", format="date-time", example="2025-04-15 10:00:00")
 *             )
 *         )
 *     )
 * )
 */
public function update(CampaignPlanningUpdateRequest $request, int $id)
{
    $campaignPlanning = CampaignPlanning::findOrFail($id);
    $data = $request->validated();
    $campaignPlanning->update($data);
    $campaignPlanning->refresh();

    return new CampaignPlanningResource($campaignPlanning);
}

    /**
     * @OA\Delete(
     *     path="/api/campaign-plannings/{id}",
     *     summary="Delete campaign planning",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Campaign Planning ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content"
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $campaignPlanning = CampaignPlanning::findOrFail($id);
        $campaignPlanning->delete();
        
        return response()->noContent();
    }
}