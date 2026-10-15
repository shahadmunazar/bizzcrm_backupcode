<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceFingerprint;
use App\Models\AttendancePunch;
use App\Models\Box;
use Yajra\DataTables\DataTables;

class MobileAttendanceReportController extends Controller
{
    public function fingerprints(Request $request, $box_slug)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $box = Box::where('slug', $box_slug)->firstOrFail();
            
            $query = AttendanceFingerprint::select('attendance_fingerprints.*', 'users.name as user_name', 'device_registers.device_name')
                ->leftJoin('users', 'attendance_fingerprints.user_id', '=', 'users.id')
                ->leftJoin('device_registers', 'attendance_fingerprints.device_id', '=', 'device_registers.device_id')
                ->where('attendance_fingerprints.boxes_id', $box->id);
                
            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row) use ($box_slug) {
                    $btn = '';
                    if (strtolower($row->status) === 'active') {
                        $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-warning btn-sm revoke-fingerprint-btn"><i class="ti ti-close"></i> Revoke</a> ';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm delete-fingerprint-btn"><i class="ti ti-trash"></i> Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('frontend.mobile_attendance.fingerprints');
    }

    public function revokeFingerprint(Request $request, $box_slug, $id)
    {
        $fingerprint = AttendanceFingerprint::find($id);
        if ($fingerprint) {
            $fingerprint->status = 'revoked';
            $fingerprint->save();
            return response()->json(['success' => 'Fingerprint revoked successfully.']);
        }
        return response()->json(['error' => 'Fingerprint not found.'], 404);
    }

    public function destroyFingerprint(Request $request, $box_slug, $id)
    {
        $fingerprint = AttendanceFingerprint::find($id);
        if ($fingerprint) {
            $fingerprint->delete();
            return response()->json(['success' => 'Fingerprint deleted successfully.']);
        }
        return response()->json(['error' => 'Fingerprint not found.'], 404);
    }

    public function punches(Request $request, $box_slug)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $box = Box::where('slug', $box_slug)->firstOrFail();
            
            $query = AttendancePunch::select('attendance_punches.*', 'users.name as user_name', 'device_registers.device_name')
                ->leftJoin('users', 'attendance_punches.user_id', '=', 'users.id')
                ->leftJoin('device_registers', 'attendance_punches.device_id', '=', 'device_registers.device_id')
                ->where('attendance_punches.boxes_id', $box->id);
                
            return Datatables::of($query)
                ->addIndexColumn()
                ->make(true);
        }
        return view('frontend.mobile_attendance.punches');
    }
}
