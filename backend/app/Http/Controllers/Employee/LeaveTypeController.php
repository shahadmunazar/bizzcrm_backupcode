<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\BaseController;
use App\Services\LeaveTypeService;
use Illuminate\Http\Request;

class LeaveTypeController extends BaseController
{
    protected $leaveTypeService;

    public function __construct(LeaveTypeService $leaveTypeService)
    {
        $this->leaveTypeService = $leaveTypeService;
    }

    public function LeaveTyperequest(Request $request)
    {
        return $this->leaveTypeService->LeaveTyperequest($request);
    }

    public function leave_type_update(Request $request)
    {
        return $this->leaveTypeService->leave_type_update($request);
    }

    public function leave_destroy(Request $request)
    {
        return $this->leaveTypeService->leave_destroy($request);
    }

    public function getDataLeaveType(Request $request)
    {
        return $this->leaveTypeService->getDataLeaveType($request);
    }

    public function leave_type_store(Request $request)
    {
        return $this->leaveTypeService->leave_type_store($request);
    }

    public function ToggleLeaveStatus(Request $request)
    {
        return $this->leaveTypeService->ToggleLeaveStatus($request);
    }
}
