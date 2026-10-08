<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Leads\ActivityController;

class WorkingLocationApiController extends Controller
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
        return $this->webController->getWorkingLocations($request);
    }

    public function store(Request $request, $box_slug, $lead_id)
    {
        $request->merge(['lead_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->addWorkingLocation($request);
    }

    public function update(Request $request, $box_slug, $lead_id, $location_id)
    {
        $request->merge(['lead_id' => $lead_id, 'id' => $location_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->saveWorkingLocation($request);
    }

    public function destroy(Request $request, $box_slug, $lead_id, $location_id)
    {
        $request->merge(['id' => $location_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->deleteWorkingLocation($request);
    }

    public function getStates(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getStates($request);
    }

    public function getCities(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getCities($request);
    }

    public function getDivisions(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getDivisions($request);
    }
}
