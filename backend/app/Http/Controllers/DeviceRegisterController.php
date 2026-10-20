<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRegister;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class DeviceRegisterController extends Controller
{
    public function index(Request $request, $box_slug)
    {
        if ($request->ajax()) {
            $data = DeviceRegister::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function($row) {
                    if (strtolower($row->status) === 'active') {
                        return '<span class="badge bg-success">Active</span>';
                    } elseif (strtolower($row->status) === 'inactive' || strtolower($row->status) === 'revoked') {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-warning text-dark">Pending</span>';
                    }
                })
                ->addColumn('action', function($row) use ($box_slug) {
                    $btn = '';
                    if (strtolower($row->status) === 'pending approval' || strtolower($row->status) === 'pending_approval') {
                        $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-target="#pendingApproveModal" data-id="'.$row->id.'" class="btn btn-primary btn-sm pending-approve-btn"><i class="ti ti-check"></i> Approve</a> ';
                    } elseif (strtolower($row->status) === 'revoked' || strtolower($row->status) === 'inactive') {
                        $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-target="#approveModal" data-id="'.$row->id.'" class="btn btn-success btn-sm approve-device-btn"><i class="ti ti-check"></i> Mark Active</a> ';
                    } elseif (strtolower($row->status) === 'active') {
                        $btn .= '<a href="javascript:void(0)" data-toggle="modal" data-target="#revokeModal" data-id="'.$row->id.'" class="btn btn-warning btn-sm revoke-device-btn"><i class="ti ti-close"></i> Mark Inactive</a> ';
                    }
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm delete-device"><i class="ti ti-trash"></i> Delete</a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('frontend.device_register.index');
    }

    public function store(Request $request, $box_slug)
    {
        $request->validate([
            'device_name' => 'required',
            'platform' => 'required',
            'status' => 'required',
        ]);

        $device = new DeviceRegister();
        $device->fill($request->all());
        $device->created_by = Auth::id();
        $device->save();

        return response()->json(['success' => 'Device saved successfully.']);
    }

    public function edit($box_slug, $id)
    {
        $device = DeviceRegister::find($id);
        return response()->json($device);
    }

    public function update(Request $request, $box_slug)
    {
        $request->validate([
            'device_name' => 'required',
            'platform' => 'required',
            'status' => 'required',
            'id' => 'required'
        ]);

        $device = DeviceRegister::find($request->id);
        if($device) {
            $device->fill($request->all());
            $device->updated_by = Auth::id();
            $device->save();
            return response()->json(['success' => 'Device updated successfully.']);
        }
        return response()->json(['error' => 'Device not found.'], 404);
    }

    public function approve(Request $request, $box_slug, $id)
    {
        $device = DeviceRegister::find($id);
        if($device) {
            $device->status = 'Active';
            $device->updated_by = Auth::id();
            $device->save();
            return response()->json(['success' => 'Device marked as active successfully.']);
        }
        return response()->json(['error' => 'Device not found.'], 404);
    }

    public function revoke(Request $request, $box_slug, $id)
    {
        $device = DeviceRegister::find($id);
        if($device) {
            $device->status = 'Inactive';
            $device->updated_by = Auth::id();
            $device->save();
            return response()->json(['success' => 'Device marked as inactive successfully.']);
        }
        return response()->json(['error' => 'Device not found.'], 404);
    }

    public function destroy($box_slug, $id)
    {
        $device = DeviceRegister::find($id);
        if($device) {
            $device->deleted_by = Auth::id();
            $device->save();
            $device->delete();
            return response()->json(['success' => 'Device deleted successfully.']);
        }
        return response()->json(['error' => 'Device not found.'], 404);
    }
}
