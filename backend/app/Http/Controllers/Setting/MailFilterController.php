<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MailFilterController extends Controller
{
    public function getFormData(Request $request)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['success' => false, 'message' => 'Box not found.'], 403);
        }
        $boxId = $boxUser->box_id;

        $gmailInboxes = \App\Models\GmailInbox::where('box_id', $boxId)->select('id', 'email')->get();
        $leadSourceTemplates = \App\Models\LeadSourceTemplate::where('status', 'Active')->select('id', 'name')->get();
        $leadSources = \App\Models\CrmSource::where('box_id', $boxId)->select('id', 'name')->get();
        $leadsStatus = \App\Models\ContactStage::where('box_id', $boxId)->select('id', 'name')->get();
        
        $boxUsers = \App\Models\BoxUser::where('box_id', $boxId)->with('user:id,name,email')->get()->map(function($bu) {
            return [
                'id' => $bu->user->id ?? '',
                'name' => $bu->user->name ?? '',
                'email' => $bu->user->email ?? ''
            ];
        })->filter(function($u) {
            return !empty($u['id']);
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'gmailInboxes' => $gmailInboxes,
                'leadSourceTemplates' => $leadSourceTemplates,
                'leadSources' => $leadSources,
                'leadsStatus' => $leadsStatus,
                'boxUsers' => $boxUsers
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'inbox_id' => 'required|exists:gmail_inboxes,id',
            'lead_source_id' => 'required|exists:crm_sources,id',
            'template_id' => 'required|exists:lead_source_templates,id',
        ]);

        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            return response()->json(['success' => false, 'message' => 'Box not found.'], 403);
        }

        try {
            \App\Models\MailFilter::create([
                'box_id' => $boxUser->box_id,
                'inbox_id' => $request->inbox_id,
                'name' => $request->name,
                'from' => $request->from,
                'subject' => $request->subject,
                'has_words' => is_array($request->has_words) ? implode(',', $request->has_words) : $request->has_words,
                'template_id' => $request->template_id,
                'status' => $request->status ?? 'Active',
                'assigned_to' => ($request->round_robin == '1') ? null : $request->assigned_to,
                'lead_source_id' => $request->lead_source_id,
                'lead_status_id' => $request->lead_status_id,
                'round_robin' => $request->round_robin ?? '0',
                'round_robin_users' => ($request->round_robin == '1') ? $request->round_robin_users : [],
                'enable_shifts' => $request->enable_shifts ?? 'No',
                'shifts' => $request->shifts ?? [],
                'added_by' => $user->id,
                'last_updated_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mail filter created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create mail filter: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request, $box_slug, $inbox_id)
    {
        $user = Auth::user();
        $boxUser = BoxUser::where('user_id', $user->id)->first();
        if (!$boxUser) {
            abort(403, 'Box not found.');
        }

        $inbox = \App\Models\GmailInbox::where('id', $inbox_id)
            ->where('box_id', $boxUser->box_id)
            ->firstOrFail();

        $filters = \App\Models\MailFilter::where('inbox_id', $inbox_id)
            ->with(['template', 'leadSource', 'leadStatus', 'assignee'])
            ->get();

        $users = \App\Models\User::whereHas('boxUsers', function($q) use ($boxUser) {
            $q->where('box_id', $boxUser->box_id);
        })->get(['id', 'name', 'email']);

        return view('settings.mail-filters-list', compact('inbox', 'filters', 'boxUser', 'users'));
    }
}
