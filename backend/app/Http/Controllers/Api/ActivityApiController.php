<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Leads\ActivityController;

class ActivityApiController extends Controller
{
    protected $webController;

    public function __construct(ActivityController $webController)
    {
        $this->webController = $webController;
    }

    public function index(Request $request, $box_slug, $lead_id)
    {
        $request->merge(['lead_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getActivities($request);
    }

    public function getAllActivities(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getAllActivities($request);
    }

    public function store(Request $request, $box_slug, $lead_id)
    {
        $request->merge(['lead_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->store($request);
    }

    public function storeActivity(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->store($request);
    }
    
    public function update(Request $request, $box_slug, $lead_id, $activity_id)
    {
        $request->merge(['activity_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->update($request, $box_slug, $activity_id);
    }

    public function destroy(Request $request, $box_slug, $lead_id, $activity_id)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->destroy($request, $box_slug, $activity_id);
    }

    public function changeStatus(Request $request, $box_slug, $lead_id, $activity_id)
    {
        // Activity status changes typically go through the update endpoint
        $request->merge(['activity_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->update($request, $box_slug, $activity_id);
    }

    public function getActivityOptions(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getActivityOptions($request);
    }

    public function getActivityTypes(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getActivityTypes($request);
    }

    public function getActivityStatuses(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getActivityStatuses($request);
    }
}
