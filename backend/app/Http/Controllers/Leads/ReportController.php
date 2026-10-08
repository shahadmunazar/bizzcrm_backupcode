<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MyReport;
use App\Models\BoxUser;
use App\Models\CrmSource;
use App\Models\ContactStage;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index($box_slug)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();
            
            if (!$boxUser) {
                return response()->json(['status' => false, 'message' => 'Box not found.'], 404);
            }

            $myReports = MyReport::where('box_id', $boxUser->box_id)
                ->where('user_id', $user->id)
                ->get();

            $sharedReports = MyReport::where('box_id', $boxUser->box_id)
                ->where('user_id', '!=', $user->id)
                ->where('is_public', true)
                ->get();

            return response()->json([
                'status' => true,
                'my_reports' => $myReports,
                'shared_reports' => $sharedReports
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, $box_slug)
    {
        try {
            $user = Auth::user();
            $boxUser = BoxUser::where('user_id', $user->id)->first();

            $data = $request->all();
            $data['box_id'] = $boxUser->box_id;
            $data['user_id'] = $user->id;
            $data['is_public'] = $request->has('is_public');
            $data['show_in_report'] = $request->has('show_in_report');
            $data['is_default'] = $request->has('is_default');

            if ($data['is_default']) {
                MyReport::where('box_id', $boxUser->box_id)
                    ->where('user_id', $user->id)
                    ->update(['is_default' => false]);
            }

            $report = MyReport::create($data);

            return response()->json(['status' => true, 'message' => 'Report saved successfully', 'data' => $report]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $box_slug, $id)
    {
        try {
            $user = Auth::user();
            $report = MyReport::where('id', $id)->where('user_id', $user->id)->firstOrFail();

            $data = $request->all();
            $data['is_public'] = $request->has('is_public');
            $data['show_in_report'] = $request->has('show_in_report');
            $data['is_default'] = $request->has('is_default');

            if ($data['is_default']) {
                MyReport::where('box_id', $report->box_id)
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $report->update($data);

            return response()->json(['status' => true, 'message' => 'Report updated successfully', 'data' => $report]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($box_slug, $id)
    {
        try {
            $user = Auth::user();
            $report = MyReport::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $report->delete();

            return response()->json(['status' => true, 'message' => 'Report deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
