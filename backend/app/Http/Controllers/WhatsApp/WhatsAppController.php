<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Activity, Box, Call, ContactStage, Lead, User,
    WhatsAppBusinessAccount, WhatsAppCampaign, WhatsAppCampaignNumber,
    WhatsAppChatSetting, WhatsAppMessage, WhatsAppPhoneNumber,
    WhatsAppProject, WhatsAppTemplate, WhatsAppGeneratedWidget
};
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class WhatsAppController extends Controller
{
    public function index(Request $request, $boxSlug, $leadId = null)
    {
        $box = Box::where('slug', $boxSlug)->first();

        if (!$box) {
            $box = $request->get('box');
        }

        if (!$box) {
            abort(404);
        }
        // Initial page load should be instant. Sidebar data is fetched via AJAX.
        $activeLead = null;
        if ($leadId) {
            $activeLead = Lead::find($leadId);
        }

        $templates = \App\Models\WhatsAppTemplate::where('box_id', $box->id)->where('whats_app_status', 'APPROVED')->get();
        $users = \App\Models\User::whereHas('boxes', function($q) use ($box) {
            $q->where('boxes.id', $box->id);
        })->get();

        return view('whatsapp.chats', compact('box', 'activeLead', 'templates', 'users'));

    }

    public function getMessages(Request $request, $boxSlug, $leadId)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        $unreadCountBeforeRead = WhatsAppMessage::where('box_id', $box->id)
            ->where('lead_id', $leadId)
            ->where('type', 'INCOMING')
            ->where('is_read', false)
            ->count();

        // Mark incoming messages as read
        WhatsAppMessage::where('box_id', $box->id)
            ->where('lead_id', $leadId)
            ->where('type', 'INCOMING')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Check for disappearing messages setting and process via Queue
        $settings = WhatsAppChatSetting::where('box_id', $box->id)
            ->where('lead_id', $leadId)
            ->whereNotNull('disappearing_duration')
            ->where('disappearing_duration', '!=', 'off')
            ->first();

        if ($settings) {
            \App\Jobs\ProcessDisappearingMessagesJob::dispatch($box->id, $leadId, $settings->disappearing_duration);
        }
        if ($request->has('after_id')) {
            $messages = WhatsAppMessage::where('box_id', $box->id)
                ->where('lead_id', $leadId)
                ->where(function($q) use ($request) {
                    $q->where('id', '>', $request->input('after_id'))
                      ->orWhere('updated_at', '>=', now()->subSeconds(15));
                })
                ->with(['campaign.template', 'replyTo'])
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $messages = WhatsAppMessage::where('box_id', $box->id)
                ->where('lead_id', $leadId)
                ->with(['campaign.template', 'replyTo'])
                ->orderBy('created_at', 'desc')
                ->skip($request->input('offset', 0))
                ->take($request->input('limit', 20))
                ->get()
                ->reverse()
                ->values();
        }
        $pinnedMessage = WhatsAppMessage::where('lead_id', $leadId)
            ->where('is_pinned', true)
            ->where(function($q) {
                $q->whereNull('pin_expires_at')
                  ->orWhere('pin_expires_at', '>', now());
            })
            ->first();

        $lead = Lead::with(['leadStatus', 'leadSource', 'assignedTo', 'updatedBy'])->find($leadId);
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'lead' => $lead,
            'pinnedMessage' => $pinnedMessage,
            'unreadCount' => $unreadCountBeforeRead
        ]);
    }

    public function sendMessage(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        $leadId = $request->input('lead_id');
        $text = $request->input('text');

        if (!$leadId || !$text) {
            return response()->json(['success' => false, 'message' => 'Invalid data']);
        }

        $lead = Lead::find($leadId);
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found']);
        }

        // Backend 24h rule check
        $lastIncoming = \App\Models\WhatsAppMessage::where('lead_id', $lead->id)
            ->where('type', 'INCOMING')
            ->latest('created_at')
            ->first();

        if (!$lastIncoming || $lastIncoming->created_at->diffInHours(now()) >= 24) {
            return response()->json([
                'success' => false,
                'message' => 'Last customer message was over 24h ago. Normal messaging is locked. Please send a template.',
                'error_code' => '131047'
            ]);
        }

        $campaign = $this->getOrCreateDirectCampaign($box, $lead, 'Direct Custom Messages');

        $campaignNumber = \App\Models\WhatsAppCampaignNumber::create([
            'box_id' => $box->id,
            'whatsapp_campaign_id' => $campaign->id,
            'lead_id' => $lead->id,
            'phone' => $lead->phone,
            'name' => $lead->name,
            'status' => 'PENDING',
        ]);

        $whatsappMessage = WhatsAppMessage::create([
            'box_id' => $box->id,
            'lead_id' => $lead->id,
            'whatsapp_campaign_id' => $campaign->id,
            'type' => 'OUTGOING',
            'sender_id' => auth()->id(),
            'text' => $text,
            'reply_to_message_id' => $request->input('reply_to_message_id'),
            'status' => 'PENDING',
            'is_sent' => false,
            'added_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $waHelper = new \App\Helpers\WhatsApp($box);
        $waHelper->sendDirectMessage($whatsappMessage);

        if ($whatsappMessage->is_sent) {
            $campaignNumber->update(['status' => 'SENT']);
            $this->logWhatsAppActivity($box->id, $leadId, 'Direct Message', $text);
        } else {
            $campaignNumber->update(['status' => 'FAILED']);
        }

        return response()->json([
            'success' => $whatsappMessage->is_sent,
            'message' => $whatsappMessage
        ]);
    }

    public function reactMessage(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $messageId = $request->input('message_id');
        $emoji = (string) $request->input('emoji', '');

        $whatsappMessage = WhatsAppMessage::find($messageId);
        if (!$whatsappMessage) {
            return response()->json(['success' => false, 'message' => 'Message not found']);
        }

        if (!$whatsappMessage->message_id) {
            return response()->json(['success' => false, 'message' => 'Cannot react to a message that has no WhatsApp ID (wamid)']);
        }

        $lead = Lead::find($whatsappMessage->lead_id);
        if (!$lead) return response()->json(['success' => false, 'message' => 'Lead not found']);

        $waHelper = new \App\Helpers\WhatsApp($box);
        
        // Strip non-numeric to get the phone
        $number = $lead->mobile ?: ($lead->phone ?: $lead->sec_phone);
        if (!$number) return response()->json(['success' => false, 'message' => 'Lead has no phone number']);
        $number = preg_replace('/[^0-9]/', '', $number);
        if (strlen($number) == 10) $number = "91" . $number;

        $response = $waHelper->sendReaction($number, $whatsappMessage->message_id, $emoji);

        if ($response['success']) {
            $whatsappMessage->user_reaction = $emoji;
            $whatsappMessage->save();
            return response()->json(['success' => true, 'reaction' => $emoji]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send reaction', 'error' => $response['error'] ?? 'Unknown error']);
    }

    public function deleteMessage($boxSlug, $id)
    {
        $message = WhatsAppMessage::find($id);
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found']);
        }
        $message->delete();
        return response()->json(['success' => true]);
    }

    public function pinMessage(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $messageId = $request->input('message_id');
        $duration = $request->input('duration', '7_days');

        $message = WhatsAppMessage::find($messageId);
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found']);
        }

        // Unpin other messages for this lead
        WhatsAppMessage::where('lead_id', $message->lead_id)
            ->where('is_pinned', true)
            ->update(['is_pinned' => false]);

        // Calculate expiration date
        $expiresAt = null;
        if ($duration === '24_hours') {
            $expiresAt = now()->addDay();
        } elseif ($duration === '7_days') {
            $expiresAt = now()->addDays(7);
        } elseif ($duration === '30_days') {
            $expiresAt = now()->addDays(30);
        }

        $message->is_pinned = true;
        $message->pinned_at = now();
        $message->pin_expires_at = $expiresAt;
        $message->save();

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function unpinMessage(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $leadId = $request->input('lead_id');
        if (!$leadId) return response()->json(['success' => false, 'message' => 'Invalid lead ID']);

        WhatsAppMessage::where('lead_id', $leadId)
            ->where('is_pinned', true)
            ->update(['is_pinned' => false]);

        return response()->json(['success' => true]);
    }

    public function starMessage(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $messageId = $request->input('message_id');
        $message = WhatsAppMessage::find($messageId);
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Message not found']);
        }

        $message->is_starred = !$message->is_starred;
        $message->save();

        return response()->json([
            'success' => true,
            'is_starred' => $message->is_starred
        ]);
    }

    public function sendMedia(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $leadId = $request->input('lead_id');
        $file = $request->file('file');
        $caption = $request->input('caption');

        if (!$leadId || !$file) return response()->json(['success' => false, 'message' => 'Invalid data']);

        $lead = Lead::find($leadId);
        if (!$lead) return response()->json(['success' => false, 'message' => 'Lead not found']);
        
        // Backend 24h rule check
        $lastIncoming = \App\Models\WhatsAppMessage::where('lead_id', $lead->id)
            ->where('type', 'INCOMING')
            ->latest('created_at')
            ->first();

        if (!$lastIncoming || $lastIncoming->created_at->diffInHours(now()) >= 24) {
            return response()->json([
                'success' => false,
                'message' => 'Last customer message was over 24h ago. Normal messaging is locked. Please send a template.',
                'error_code' => '131047'
            ]);
        }
        
        // Save file locally
        $filename = time() . '_' . $file->getClientOriginalName();
        $uploadPath = "uploads/{$box->id}/whatsapp/outgoing";
        $path = $file->storeAs($uploadPath, $filename, 'public');

        if (!$path) {
            Log::error("❌ [WhatsApp] File Upload Failed to Storage", [
                'filename' => $filename,
                'upload_path' => $uploadPath,
                'lead_id' => $leadId
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to save file on server. Check folder permissions.']);
        }

        $mime = $file->getMimeType();
        $type = 'document';
        if (strpos($mime, 'image') !== false) $type = 'image';
        elseif (strpos($mime, 'video') !== false) $type = 'video';
        elseif (strpos($mime, 'audio') !== false) $type = 'audio';

        $campaign = $this->getOrCreateDirectCampaign($box, $lead, 'Direct Custom Messages');

        $campaignNumber = \App\Models\WhatsAppCampaignNumber::create([
            'box_id' => $box->id,
            'whatsapp_campaign_id' => $campaign->id,
            'lead_id' => $lead->id,
            'phone' => $lead->phone,
            'name' => $lead->name,
            'status' => 'PENDING',
        ]);

        $whatsappMessage = WhatsAppMessage::create([
            'box_id' => $box->id,
            'lead_id' => $lead->id,
            'whatsapp_campaign_id' => $campaign->id,
            'type' => 'OUTGOING',
            'sender_id' => auth()->id(),
            'text' => $caption,
            'reply_to_message_id' => $request->input('reply_to_message_id'),
            'media' => [
                'path' => '/storage/' . $path,
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $mime,
                'type' => $type
            ],
            'status' => 'PENDING',
            'is_sent' => false,
            'added_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $waHelper = new \App\Helpers\WhatsApp($box);
        $waHelper->sendDirectMessage($whatsappMessage);

        if ($whatsappMessage->is_sent) {
            $campaignNumber->update(['status' => 'SENT']);
            $this->logWhatsAppActivity($box->id, $leadId, 'Media Message (' . ucfirst($type) . ')', $caption);
        } else {
            $campaignNumber->update(['status' => 'FAILED']);
        }

        return response()->json([
            'success' => $whatsappMessage->is_sent, 
            'message' => $whatsappMessage->refresh()
        ]);
    }

    public function syncMeta(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $waHelper = new \App\Helpers\WhatsApp($box);
        // getWABA handles discovery and syncing of portfolios, accounts and phones
        $result = $waHelper->getWABA($box, $request);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Data synced from Meta successfully.',
                'details' => $result['data']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sync failed: ' . ($result['error'] ?? 'Unknown error')
        ]);
    }

    public function createProject(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }
        $accounts = collect();
        $phoneNumbers = collect();
        $projects = collect();

        if ($box) {
            $accounts = WhatsAppBusinessAccount::where('box_id', $box->id)->with('portfolio')->get();
            $phoneNumbers = WhatsAppPhoneNumber::where('box_id', $box->id)->get();
            
            // Auto-sync accounts to projects for consistency
            foreach ($accounts as $account) {
                    WhatsAppProject::updateOrCreate(
                        ['box_id' => $box->id, 'name' => $account->name],
                        [
                            'whatsapp_business_account_id' => $account->id, 
                            'is_active' => 1,
                            'updated_by' => auth()->id() ?? 1
                        ]
                    );
            }

            $projects = WhatsAppProject::where('box_id', $box->id)->with('account')->get();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'accounts' => $accounts,
                'phoneNumbers' => $phoneNumbers,
                'projects' => $projects
            ]);
        }

        return view('whatsapp.create_project', compact('box', 'accounts', 'phoneNumbers', 'projects'));
    }

    public function storeProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }

        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        $project = WhatsAppProject::create([
            'box_id' => $box->id,
            'name' => $request->name,
            'added_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'project' => $project
        ]);
    }

    public function linkAccount(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:whats_app_projects,id',
            'whatsapp_business_account_id' => 'required|exists:whats_app_accounts,id',
        ]);

        $project = WhatsAppProject::find($request->project_id);
        $project->update([
            'whatsapp_business_account_id' => $request->whatsapp_business_account_id,
            'updated_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account linked to project successfully',
            'project' => $project->load('account')
        ]);
    }

    public function createTemplate(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }

        $accounts = collect();
        if ($box) {
            $accounts = WhatsAppBusinessAccount::where('box_id', $box->id)->get();
        }

        return view('whatsapp.create_template', compact('box', 'accounts'));
    }



    public function campaign(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }

        if ($request->ajax()) {
            $query = WhatsAppCampaign::where('box_id', $box->id)
                ->with(['template', 'chatbot'])
                ->withCount(['messages']);

            // Apply Project Filter
            if ($request->filled('project_id')) {
                $project = \App\Models\WhatsAppProject::with('account')->find($request->project_id);
                if ($project && $project->account && $project->account->waba_id) {
                    $query->whereHas('template', function($q) use ($project) {
                        $q->where('waba_id', $project->account->waba_id);
                    });
                }
            }

            // Apply Date Filters
            if ($request->filled('start_date') && $request->filled('end_date')) {
                // Carbon parse the dates to be safe
                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            }

            // Apply Custom Sort
            if ($request->filled('custom_sort')) {
                $sort = $request->custom_sort;
                if ($sort === 'ascending') {
                    $query->orderBy('name', 'asc');
                } else if ($sort === 'descending') {
                    $query->orderBy('name', 'desc');
                } else if ($sort === 'recently_viewed') {
                    // Assuming no recently viewed column, fallback to created_at desc
                    $query->orderBy('created_at', 'desc');
                } else if ($sort === 'recently_added') {
                    $query->orderBy('created_at', 'desc');
                }
            }

            // Calculate stats efficiently using a fresh query to avoid select conflicts with withCount
            $statsQuery = WhatsAppCampaign::where('box_id', $box->id);
            
            // Apply Project Filter for stats
            if ($request->filled('project_id')) {
                $project = \App\Models\WhatsAppProject::with('account')->find($request->project_id);
                if ($project && $project->account && $project->account->waba_id) {
                    $statsQuery->whereHas('template', function($q) use ($project) {
                        $q->where('waba_id', $project->account->waba_id);
                    });
                }
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                $statsQuery->whereBetween('created_at', [$start, $end]);
            }
            $stats = $statsQuery->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "ACTIVE" THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status IN ("PENDING", "DRAFT") THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "COMPLETED" THEN 1 ELSE 0 END) as completed
            ')->first();

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('checkbox', function($row) {
                    return '<label class="checkboxs"><input type="checkbox" class="campaign-checkbox" value="'.$row->id.'"><span class="checkmarks"></span></label>';
                })
                ->addColumn('icon', function($row) {
                    return '<span class="avatar avatar-sm bg-success text-white rounded-circle"><i class="fa-brands fa-whatsapp fs-14"></i></span>';
                })
                ->addColumn('name', function($row) use ($box) {
                    $url = route('whatsapp.campaign.show', ['box_slug' => $box->slug, 'id' => $row->id]);
                    return '<a href="'.$url.'" class="text-dark fw-bold hover-primary">'.($row->name ?: 'Unnamed Campaign').'</a>';
                })
                ->addColumn('template_name', function($row) {
                    return '<span class="text-primary cursor-pointer preview-template" data-id="'.$row->id.'" style="cursor:pointer; text-decoration: underline;">'.($row->template ? $row->template->name : 'Unknown').'</span>';
                })
                ->addColumn('messages_count', function($row) {
                    return '<span class="badge bg-soft-info text-info cursor-pointer view-numbers" data-id="'.$row->id.'" title="Click to view numbers">'.($row->messages_count ?: 0).' Sent</span>';
                })
                ->addColumn('schedule_at', function($row) {
                    return $row->schedule_at ? \Carbon\Carbon::parse($row->schedule_at)->format('d M Y, H:i') : 'Immediate';
                })
                ->addColumn('status', function($row) {
                    $upperStatus = strtoupper($row->status ?: 'DRAFT');
                    $badgeClass = 'bg-warning-light text-warning';
                    if ($upperStatus === 'COMPLETED') $badgeClass = 'bg-success-light text-success';
                    else if ($upperStatus === 'ACTIVE') $badgeClass = 'bg-primary-light text-primary';
                    return '<span class="badge badge-pill '.$badgeClass.'">'.$upperStatus.'</span>';
                })
                ->addColumn('created_at', function($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d M Y');
                })
                ->addColumn('chatbot', function($row) {
                    if ($row->whatsapp_chatbot_id && $row->chatbot) {
                        return '<span class="badge bg-soft-primary text-primary"><i class="fa-solid fa-robot me-1"></i> '.$row->chatbot->name.'</span> <a href="javascript:void(0);" class="text-muted ms-1 btn-assign-chatbot" data-id="'.$row->id.'" data-chatbot-id="'.$row->whatsapp_chatbot_id.'" title="Edit/Unassign"><i class="ti ti-edit"></i></a>';
                    } else {
                        return '<a href="javascript:void(0);" class="btn btn-sm btn-soft-secondary btn-assign-chatbot" data-id="'.$row->id.'" data-chatbot-id=""><i class="ti ti-plus me-1"></i> Assign</a>';
                    }
                })
                ->addColumn('action', function($row) {
                    $upperStatus = strtoupper($row->status ?: 'DRAFT');
                    $actionsHtml = '<a class="me-2 p-2 preview-template" href="javascript:void(0);" data-id="'.$row->id.'" title="Preview"><i class="ti ti-eye"></i></a>';
                    
                    if (in_array($upperStatus, ['SENT', 'COMPLETED', 'STOPPED'])) {
                        $actionsHtml .= '<a class="p-2 btn-delete-campaign text-danger" href="javascript:void(0);" data-id="'.$row->id.'" title="Delete"><i class="ti ti-trash"></i></a>';
                    } else if ($upperStatus === 'SENDING') {
                        $actionsHtml .= '<a class="me-2 p-2 btn-status-campaign text-warning" href="javascript:void(0);" data-id="'.$row->id.'" data-status="PAUSED" title="Pause"><i class="ti ti-player-pause"></i></a>';
                        $actionsHtml .= '<a class="p-2 btn-status-campaign text-danger" href="javascript:void(0);" data-id="'.$row->id.'" data-status="STOPPED" title="Stop"><i class="ti ti-square"></i></a>';
                    } else if ($upperStatus === 'PAUSED') {
                        $actionsHtml .= '<a class="me-2 p-2 btn-status-campaign text-success" href="javascript:void(0);" data-id="'.$row->id.'" data-status="SENDING" title="Resume"><i class="ti ti-player-play"></i></a>';
                        $actionsHtml .= '<a class="p-2 btn-status-campaign text-danger" href="javascript:void(0);" data-id="'.$row->id.'" data-status="STOPPED" title="Stop"><i class="ti ti-square"></i></a>';
                    } else {
                        $actionsHtml .= '<a class="me-2 p-2 btn-edit-campaign" href="javascript:void(0);" data-id="'.$row->id.'"><i class="ti ti-edit"></i></a>';
                        $actionsHtml .= '<a class="p-2 btn-delete-campaign text-danger" href="javascript:void(0);" data-id="'.$row->id.'" title="Delete"><i class="ti ti-trash"></i></a>';
                    }
                    return '<div class="edit-delete-action">'.$actionsHtml.'</div>';
                })
                ->rawColumns(['checkbox', 'icon', 'name', 'template_name', 'messages_count', 'status', 'chatbot', 'action'])
                ->with([
                    'total' => $stats->total ?? 0,
                    'active' => $stats->active ?? 0,
                    'pending' => $stats->pending ?? 0,
                    'completed' => $stats->completed ?? 0
                ])
                ->make(true);
        }

        $accounts = collect();
        $phoneNumbers = collect();
        $templates = collect();
        $chatbots = collect();
        $projects = collect();

        if ($box) {
            $accounts = WhatsAppBusinessAccount::where('box_id', $box->id)->get();
            $phoneNumbers = WhatsAppPhoneNumber::where('box_id', $box->id)->get();
            $templates = WhatsAppTemplate::where('box_id', $box->id)->where('whats_app_status', 'APPROVED')->get();
            $chatbots = \App\Models\WhatsAppChatbot::where('box_id', $box->id)->where('is_active', 1)->get();
            $projects = \App\Models\WhatsAppProject::where('box_id', $box->id)->get();
        }

        return view('whatsapp.campaign', compact('box', 'accounts', 'phoneNumbers', 'templates', 'chatbots', 'projects'));
    }

    public function assignChatbot(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);

        $campaign->whatsapp_chatbot_id = $request->chatbot_id ?: null; // Handle empty string as null
        $campaign->save();

        return response()->json([
            'success' => true,
            'message' => 'Chatbot assigned successfully',
            'campaign' => $campaign
        ]);
    }

    public function getCampaignMessages(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);

        $messages = $campaign->messages()->with('lead')->limit(100)->latest()->get(); // Limit to 100 for fast modal preview

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function campaign_details()
    {
        return view('whatsapp.campaign_details');
    }

    public function campaign_complete(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        
        if ($request->ajax()) {
            $query = WhatsAppCampaign::where('box_id', $box->id)
                ->where('status', 'COMPLETED')
                ->with(['template'])
                ->withCount(['messages']);

            // Apply Project Filter
            if ($request->filled('project_id')) {
                $project = \App\Models\WhatsAppProject::with('account')->find($request->project_id);
                if ($project && $project->account && $project->account->waba_id) {
                    $query->whereHas('template', function($q) use ($project) {
                        $q->where('waba_id', $project->account->waba_id);
                    });
                }
            }

            $query->latest();

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('checkbox', function($row) {
                    return '<label class="checkboxs"><input type="checkbox" class="campaign-checkbox" value="'.$row->id.'"><span class="checkmarks"></span></label>';
                })
                ->addColumn('icon', function($row) {
                    return '<span class="avatar avatar-sm bg-success text-white rounded-circle"><i class="fa-brands fa-whatsapp fs-14"></i></span>';
                })
                ->editColumn('name', function($row) {
                    return '<span class="fw-bold text-dark">'.($row->name ?? 'Unnamed Campaign').'</span>';
                })
                ->editColumn('template', function($row) {
                    $name = $row->template ? $row->template->name : 'Unknown';
                    return '<span class="text-primary cursor-pointer preview-template" data-id="'.$row->id.'" style="text-decoration: underline;">'.$name.'</span>';
                })
                ->editColumn('sent', function($row) {
                    return '<span class="badge bg-soft-info text-info cursor-pointer view-numbers" data-id="'.$row->id.'">'.$row->messages_count.' Sent</span>';
                })
                ->editColumn('schedule_at', function($row) {
                    return $row->schedule_at ? \Carbon\Carbon::parse($row->schedule_at)->format('d M Y, H:i') : 'Immediate';
                })
                ->editColumn('status', function($row) {
                    return '<span class="badge badge-pill bg-success-light text-success">COMPLETED</span>';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('action', function($row) {
                    return '
                        <div class="edit-delete-action">
                            <a class="me-2 p-2 preview-template" href="javascript:void(0);" data-id="'.$row->id.'" title="Preview"><i class="ti ti-eye"></i></a>
                            <a class="p-2 btn-delete-campaign" href="javascript:void(0);" data-id="'.$row->id.'"><i class="ti ti-trash"></i></a>
                        </div>';
                })
                ->rawColumns(['checkbox', 'icon', 'name', 'template', 'sent', 'status', 'action'])
                ->make(true);
        }

        // Stats specific to COMPLETED campaigns
        $completedCampaigns = WhatsAppCampaign::where('box_id', $box->id)->where('status', 'COMPLETED');
        
        // Apply Project Filter for stats if requested
        if ($request->filled('project_id')) {
            $project = \App\Models\WhatsAppProject::with('account')->find($request->project_id);
            if ($project && $project->account && $project->account->waba_id) {
                $completedCampaigns->whereHas('template', function($q) use ($project) {
                    $q->where('waba_id', $project->account->waba_id);
                });
            }
        }

        $completedIds = $completedCampaigns->pluck('id');
        
        $totalCompleted = $completedCampaigns->count();
        $totalSent = WhatsAppCampaignNumber::whereIn('whatsapp_campaign_id', $completedIds)->count();
        $totalDelivered = WhatsAppCampaignNumber::whereIn('whatsapp_campaign_id', $completedIds)->whereIn('status', ['sent', 'delivered', 'read'])->count();
        $totalPending = WhatsAppCampaignNumber::whereIn('whatsapp_campaign_id', $completedIds)->where('status', 'pending')->count();
        
        $templates = WhatsAppTemplate::where('box_id', $box->id)->where('whats_app_status', 'APPROVED')->get();
        $projects = \App\Models\WhatsAppProject::where('box_id', $box->id)->get();

        return view('whatsapp.campaign_complete', compact('box', 'totalCompleted', 'totalSent', 'totalDelivered', 'totalPending', 'templates', 'projects'));
    }

    public function campaign_archive(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        
        if ($request->ajax()) {
            $query = WhatsAppCampaign::where('box_id', $box->id)
                ->where('status', 'ARCHIVED')
                ->with(['template'])
                ->withCount(['messages']);

            // Apply Project Filter
            if ($request->filled('project_id')) {
                $project = \App\Models\WhatsAppProject::with('account')->find($request->project_id);
                if ($project && $project->account && $project->account->waba_id) {
                    $query->whereHas('template', function($q) use ($project) {
                        $q->where('waba_id', $project->account->waba_id);
                    });
                }
            }

            $query->latest();

            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('checkbox', function($row) {
                    return '<label class="checkboxs"><input type="checkbox" class="campaign-checkbox" value="'.$row->id.'"><span class="checkmarks"></span></label>';
                })
                ->addColumn('icon', function($row) {
                    return '<span class="avatar avatar-sm bg-secondary text-white rounded-circle"><i class="fa-brands fa-whatsapp fs-14"></i></span>';
                })
                ->editColumn('name', function($row) {
                    return '<span class="fw-bold text-muted">'.($row->name ?? 'Unnamed Campaign').'</span>';
                })
                ->editColumn('template', function($row) {
                    $name = $row->template ? $row->template->name : 'Unknown';
                    return '<span class="text-secondary">'.$name.'</span>';
                })
                ->editColumn('sent', function($row) {
                    return '<span class="badge bg-soft-secondary text-secondary">'.$row->messages_count.' Sent</span>';
                })
                ->editColumn('schedule_at', function($row) {
                    return $row->schedule_at ? \Carbon\Carbon::parse($row->schedule_at)->format('d M Y, H:i') : 'Immediate';
                })
                ->editColumn('status', function($row) {
                    return '<span class="badge badge-pill bg-light text-muted">ARCHIVED</span>';
                })
                ->editColumn('created_at', function($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('action', function($row) {
                    return '
                        <div class="edit-delete-action">
                            <a class="p-2 btn-delete-campaign" href="javascript:void(0);" data-id="'.$row->id.'"><i class="ti ti-trash"></i></a>
                        </div>';
                })
                ->rawColumns(['checkbox', 'icon', 'name', 'template', 'sent', 'status', 'action'])
                ->make(true);
        }

        $statsQuery = WhatsAppCampaign::where('box_id', $box->id);
        
        // Apply Project Filter for stats if requested
        if ($request->filled('project_id')) {
            $project = \App\Models\WhatsAppProject::with('account')->find($request->project_id);
            if ($project && $project->account && $project->account->waba_id) {
                $statsQuery->whereHas('template', function($q) use ($project) {
                    $q->where('waba_id', $project->account->waba_id);
                });
            }
        }

        $total = (clone $statsQuery)->count();
        $pending = (clone $statsQuery)->whereIn('status', ['PENDING', 'DRAFT'])->count();
        $active = (clone $statsQuery)->whereIn('status', ['ACTIVE', 'SENDING'])->count();
        $completed = (clone $statsQuery)->where('status', 'COMPLETED')->count();
        
        $templates = WhatsAppTemplate::where('box_id', $box->id)->where('whats_app_status', 'APPROVED')->get();
        $projects = \App\Models\WhatsAppProject::where('box_id', $box->id)->get();

        return view('whatsapp.campaign_archive', compact('box', 'total', 'pending', 'active', 'completed', 'templates', 'projects'));
    }

    public function linkGenerator(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }
        
        $boxId = $box ? $box->id : null;
        $savedLinks = WhatsAppGeneratedWidget::where('type', 'link')
            ->where('box_id', $boxId)
            ->latest()
            ->get();
            
        return view('whatsapp.link_generator', compact('box', 'savedLinks'));
    }

    public function buttonGenerator(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }
        
        $boxId = $box ? $box->id : null;
        $savedWidgets = WhatsAppGeneratedWidget::where('type', 'button_widget')
            ->where('box_id', $boxId)
            ->latest()
            ->get();
            
        return view('whatsapp.button_generator', compact('box', 'savedWidgets'));
    }

    public function saveGeneratedWidget(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }

        $widget = new WhatsAppGeneratedWidget();
        $widget->box_id = $box ? $box->id : null;
        $widget->type = $request->type; // 'link' or 'button_widget'
        $widget->name = $request->name;
        $widget->phone_number = $request->phone_number;
        $widget->custom_message = $request->custom_message;
        $widget->config = $request->config; // json
        $widget->generated_url_or_code = $request->generated_url_or_code;
        $widget->added_by = auth()->id();
        $widget->save();

        return response()->json(['status' => 'success', 'message' => 'Saved successfully', 'data' => $widget]);
    }

    public function dashboard(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            if ($boxSlug) {
                $box = Box::where('slug', $boxSlug)->first();
            }
        }

        $whatsappConnected = false;
        $accounts = [];
        $phoneNumbers = [];

        $totalSent = 0;
        $totalReceived = 0;
        $totalChats = 0;

        if ($box) {
            $box->refresh();
            $whatsappConnected = !empty($box->whatsapp_token)
                && isset($box->whatsapp_token['access_token'])
                && !empty($box->whatsapp_token['access_token']);

            // Fetch all discovered accounts for selection
            $allAccounts = WhatsAppBusinessAccount::where('box_id', $box->id)->get();
            $allPhoneNumbers = WhatsAppPhoneNumber::where('box_id', $box->id)->get();

            // Show ONLY active saved accounts for this box on the dashboard
            $accounts = $allAccounts->where('is_active', 1);
            $phoneNumbers = $allPhoneNumbers->where('is_active', 1);
            
            // Fetch Dynamic Stats
            $totalSent = WhatsAppMessage::where('box_id', $box->id)->where('type', 'OUTGOING')->count();
            $totalReceived = WhatsAppMessage::where('box_id', $box->id)->where('type', 'INCOMING')->count();
            
            // Calculate Total Active Chats (Matches the Sidebar 'Active' tab logic: leads with a message in the last 24 hours)
            $totalChats = \App\Models\Lead::whereHas('whatsappMessages', function($q) use ($box) {
                $q->where('box_id', $box->id)
                  ->where('created_at', '>=', now()->subHours(24));
            })->count();
            
            // Calculate Daily Limit
            $dailyLimit = 'Unknown';
            $limitRemaining = 'Unknown';
            $sentToday = 0;
            if ($phoneNumbers->count() > 0) {
                $activePhone = $phoneNumbers->first();
                $tier = 'TIER_UNKNOWN';
                
                if (!empty($activePhone->metadata) && is_array($activePhone->metadata) && isset($activePhone->metadata['status_data']['messaging_limit_tier'])) {
                    $tier = $activePhone->metadata['status_data']['messaging_limit_tier'];
                }
                
                $limitMap = [
                    'TIER_250' => 250,
                    'TIER_1K' => 1000,
                    'TIER_2K' => 2000,
                    'TIER_10K' => 10000,
                    'TIER_100K' => 100000,
                    'TIER_UNLIMITED' => 'Unlimited',
                ];
                $dailyLimit = $limitMap[$tier] ?? 250;
                
                // Special override for known account
                $activeWaba = $accounts->first();
                if ($activeWaba && $activeWaba->waba_id == '319518821236508' && $tier == 'TIER_UNKNOWN') {
                    $dailyLimit = 2000;
                }
                
                $sentToday = \App\Models\WhatsAppMessage::where('sender_id', $activePhone->whatsapp_id)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->count();
                    
                $limitRemaining = is_numeric($dailyLimit) ? max(0, $dailyLimit - $sentToday) : 'Unlimited';
            }
        }

        return view('whatsapp.whatsapp_dashboard', compact('box', 'whatsappConnected', 'accounts', 'phoneNumbers', 'totalSent', 'totalReceived', 'totalChats', 'allAccounts', 'allPhoneNumbers', 'dailyLimit', 'limitRemaining'));
    }

    public function getLeadActivityData($boxSlug, $leadId)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $lead = Lead::findOrFail($leadId);
        
        $leadStatuses = ContactStage::where('box_id', $box->id)->get();
        $users = User::whereHas('boxes', function($q) use ($box) {
            $q->where('boxes.id', $box->id);
        })->get();
        
        $activitySettings = Call::where('box_id', $box->id)->where('status', 1)->get();

        return response()->json([
            'success' => true,
            'lead' => $lead,
            'leadStatuses' => $leadStatuses,
            'users' => $users,
            'activitySettings' => $activitySettings
        ]);
    }

    public function getLeadActivities(Request $request, $boxSlug, $leadId)
    {
        $activityController = app(\App\Http\Controllers\Leads\ActivityController::class);
        $request->merge(['lead_id' => $leadId]);
        return $activityController->getActivities($request);
    }

    public function storeLeadActivity(Request $request, $boxSlug, $leadId)
    {
        // Proxy to ActivityController
        $activityController = app(\App\Http\Controllers\Leads\ActivityController::class);
        $request->merge(['lead_id' => $leadId]);
        return $activityController->store($request);
    }

    public function selectAccount(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $wabaIds = $request->input('waba_ids', []);
        $phoneIds = $request->input('phone_ids', []); // Map of waba_id => phone_id
        $preserveOthers = $request->input('preserve_others', false);
        $setActive = filter_var($request->input('set_active', true), FILTER_VALIDATE_BOOLEAN);

        if (empty($wabaIds)) {
            return response()->json(['success' => false, 'message' => 'Please select at least one account']);
        }

        if (!$preserveOthers) {
            // 1. Delete all unselected accounts and phones from this box
            WhatsAppPhoneNumber::where('box_id', $box->id)
                ->whereNotIn('whatsapp_id', array_values($phoneIds))
                ->delete();
                
            WhatsAppBusinessAccount::where('box_id', $box->id)
                ->whereNotIn('waba_id', $wabaIds)
                ->delete();
        }

        // 2. Set selected accounts status
        WhatsAppBusinessAccount::where('box_id', $box->id)
            ->whereIn('waba_id', $wabaIds)
            ->update([
                'is_active' => $setActive,
                'updated_by' => auth()->id()
            ]);
            
        // 3. Set selected phone numbers status
        $selectedPhoneIds = array_values($phoneIds);
        WhatsAppPhoneNumber::where('box_id', $box->id)
            ->whereIn('whatsapp_id', $selectedPhoneIds)
            ->update([
                'is_active' => $setActive,
                'updated_by' => auth()->id()
            ]);

        // 4. Update the primary one in the box for default/legacy support
        $primaryWabaId = $wabaIds[0];
        $primaryPhoneId = $phoneIds[$primaryWabaId] ?? null;
        
        $account = WhatsAppBusinessAccount::where('box_id', $box->id)->where('waba_id', $primaryWabaId)->first();

        $box->update([
            'whatsapp_business_id' => $primaryWabaId,
            'whatsapp_phone_id' => $primaryPhoneId,
            'whatsapp_business_portfolio_id' => $account ? $account->whatsapp_business_portfolio_id : null,
            'whatsapp_enable' => 'Yes'
        ]);

        return response()->json([
            'success' => true, 
            'message' => count($wabaIds) . ' accounts activated successfully.'
        ]);
    }

    public function disconnect(Request $request)
    {
        $boxSlug = $request->route('box_slug');
        \Illuminate\Support\Facades\Log::info("🛡️ [WhatsApp] Disconnect Triggered for: " . ($boxSlug ?? 'UNKNOWN'));

        $box = $request->get('box');
        if (!$box && $boxSlug) {
            $box = Box::where('slug', $boxSlug)->first();
        }

        if (!$box) {
            \Illuminate\Support\Facades\Log::error("❌ [WhatsApp] Disconnect failed: Box not found for slug: " . $boxSlug);
            return response()->json(['success' => false, 'message' => 'Box not found.']);
        }

        \Illuminate\Support\Facades\Log::info("✅ [WhatsApp] Disconnecting Box ID: " . $box->id);

        // 1. Clear WhatsApp specific credentials
        $box->whatsapp_token = null;
        $box->whatsapp_phone_id = null;
        $box->whatsapp_business_id = null;
        $box->whatsapp_business_portfolio_id = null;

        // 2. Clear Facebook specific integration (if applicable)
        if (\Illuminate\Support\Facades\Schema::hasColumn('boxes', 'facebook_access_token')) {
            $box->facebook_access_token = null;
        }
        
        $box->save();

        // 3. Delete cached Phone numbers first, then WABA accounts (to respect Foreign Key constraints)
        WhatsAppPhoneNumber::where('box_id', $box->id)->update(['deleted_by' => auth()->id()]);
        WhatsAppPhoneNumber::where('box_id', $box->id)->delete();
        
        WhatsAppBusinessAccount::where('box_id', $box->id)->update(['deleted_by' => auth()->id()]);
        WhatsAppBusinessAccount::where('box_id', $box->id)->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Account disconnected successfully.']);
        }

        return redirect()->back()->with('success', 'Account disconnected successfully.');
    }

    public function disconnectSingle(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false, 'message' => 'Box not found']);

        $wabaId = $request->input('waba_id');
        $phoneId = $request->input('phone_id'); // Optional phone ID for individual disconnection

        // Handle Individual Phone Disconnection
        if ($phoneId) {
            $phone = WhatsAppPhoneNumber::where('box_id', $box->id)->where('whatsapp_id', $phoneId)->first();
            if ($phone) {
                $phone->update(['deleted_by' => auth()->id()]);
                $phone->delete();
                
                // If this was the active phone, pick another one for this box
                if ($box->whatsapp_phone_id == $phoneId) {
                    $nextPhone = WhatsAppPhoneNumber::where('box_id', $box->id)->first();
                    $box->update(['whatsapp_phone_id' => $nextPhone->whatsapp_id ?? null]);
                }
                
                return response()->json(['success' => true, 'message' => 'Phone number removed successfully.']);
            }
            return response()->json(['success' => false, 'message' => 'Phone number not found.']);
        }

        // Handle Entire Account Disconnection (Original logic)
        if (!$wabaId) return response()->json(['success' => false, 'message' => 'Missing Account ID']);

        $account = WhatsAppBusinessAccount::where('box_id', $box->id)->where('waba_id', $wabaId)->first();
        if ($account) {
            WhatsAppPhoneNumber::where('box_id', $box->id)->where('target_id', $account->id)->update(['deleted_by' => auth()->id()]);
            WhatsAppPhoneNumber::where('box_id', $box->id)->where('target_id', $account->id)->delete();
            
            $account->update(['deleted_by' => auth()->id()]);
            $account->delete();
        }

        if ($box->whatsapp_business_id == $wabaId) {
            $nextAccount = WhatsAppBusinessAccount::where('box_id', $box->id)->first();
            if ($nextAccount) {
                $box->update([
                    'whatsapp_business_id' => $nextAccount->waba_id,
                    'whatsapp_phone_id' => WhatsAppPhoneNumber::where('target_id', $nextAccount->id)->first()->whatsapp_id ?? null,
                    'whatsapp_business_portfolio_id' => $nextAccount->whatsapp_business_portfolio_id
                ]);
            } else {
                $box->update([
                    'whatsapp_business_id' => null,
                    'whatsapp_phone_id' => null,
                    'whatsapp_business_portfolio_id' => null
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Account removed successfully.']);
    }

    public function getNotificationCount(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
        }

        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        // Get muted lead IDs
        $mutedLeadIds = WhatsAppChatSetting::where('box_id', $box->id)
            ->where('muted_until', '>', now())
            ->pluck('lead_id');

        $count = WhatsAppMessage::where('box_id', $box->id)
            ->where('is_read', false)
            ->where('type', 'INCOMING')
            ->whereNotIn('lead_id', $mutedLeadIds)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    public function getUnreadMessages(Request $request)
    {
        $box = $request->get('box');
        if (!$box) {
            $boxSlug = $request->route('box_slug');
            $box = Box::where('slug', $boxSlug)->first();
        }

        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        // Get muted lead IDs
        $mutedLeadIds = WhatsAppChatSetting::where('box_id', $box->id)
            ->where('muted_until', '>', now())
            ->pluck('lead_id');

        // Fetch latest messages (ONLY incoming) to show recent activity
        $messages = WhatsAppMessage::where('box_id', $box->id)
            ->where('is_read', false)
            ->where('type', 'INCOMING')
            ->whereNotIn('lead_id', $mutedLeadIds)
            ->with('lead')
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function fetchContacts(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        $search = $request->input('search');
        $offset = $request->input('offset', 0);
        $limit = 100;

        $query = Lead::where('box_id', $box->id);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('sec_phone', 'like', "%$search%");
            });
        }

        $leads = $query->select('id', 'name', 'phone', 'sec_phone')
            ->orderBy('name', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $totalCount = Lead::where('box_id', $box->id)->count();

        return response()->json([
            'success' => true,
            'data' => $leads,
            'has_more' => $leads->count() === $limit,
            'total_count' => $totalCount
        ]);
    }

    public function importExcelCampaign(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found.']);
        }

        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'template_id' => 'required',
            'excel_file' => 'required|file|max:51200|mimes:xlsx,xls,csv,txt',
            'phone_column' => 'required',
            'name_column' => 'nullable'
        ]);

        try {
            $file = $request->file('excel_file');
            $directory = 'campaign_uploads';
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Use public disk for better accessibility between web and CLI
            $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($directory, $file, $fileName);

            $campaign = WhatsAppCampaign::create([
                'box_id' => $box->id,
                'whats_app_template_id' => $request->template_id,
                'whatsapp_chatbot_id' => $request->chatbot_id ?: null,
                'name' => $request->campaign_name,
                'schedule_at' => now(),
                'status' => 'PROCESSING',
                'file_path' => $path,
                'params' => [
                    'schedule_type' => 'now',
                ],
                'added_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Dispatch the background job
            \App\Jobs\ProcessWhatsAppCampaignJob::dispatch(
                $campaign->id,
                $box->id,
                $request->phone_column,
                $request->name_column,
                $request->has('create_leads'),
                auth()->id(),
                $request->variable_map ?? []
            );

            return response()->json([
                'success' => true, 
                'message' => 'File uploaded. Processing started in background...',
                'campaign_id' => $campaign->id,
                'redirect' => route('whatsapp.campaign.show', ['box_slug' => $boxSlug, 'id' => $campaign->id])
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to start upload: ' . $e->getMessage()]);
        }
    }

    public function getCampaignProgress(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);
        
        $status = $campaign->status;
        $total = 0;
        $processed = 0;
        $percentage = 0;
        
        if ($status === 'PROCESSING') {
            // During Excel Import stage
            $total = $campaign->total_rows ?? 0;
            $processed = $campaign->processed_rows ?? 0;
            $percentage = $total > 0 ? round(($processed / $total) * 100) : 0;
        } elseif ($status === 'IMPORTED') {
            // Excel Import is 100% complete, waiting for user to start campaign
            $total = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->count();
            if ($total === 0) {
                $total = $campaign->total_rows ?? 0;
            }
            $processed = $total;
            $percentage = 100;
        } else {
            // During Sending stage (SENDING, COMPLETED, etc.)
            $total = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->count();
            if ($total === 0) {
                $total = $campaign->total_rows ?? 0;
                $processed = $campaign->processed_rows ?? 0;
            } else {
                $sent = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->whereIn('status', ['SENT', 'DELIVERED', 'READ'])->count();
                $failed = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->where('status', 'FAILED')->count();
                $processed = $sent + $failed;
            }
            $percentage = $total > 0 ? round(($processed / $total) * 100) : 0;
        }
        
        if ($percentage > 100) $percentage = 100;

        // Calculate ETA for sending phase
        $etaHuman = null;
        $estimatedDays = 0;
        $dailyLimit = 1000;
        $isLimitReached = false;
        
        if ($status === 'SENDING') {
            $whatsappNumber = WhatsAppPhoneNumber::where('box_id', $box->id)->where('is_active', 1)->first();
            if ($whatsappNumber) {
                $dailyLimit = $whatsappNumber->metadata['daily_limit'] ?? 1000;
                
                $sentToday = \App\Models\WhatsAppMessage::where('box_id', $box->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->where('status', '!=', 'Failed')
                    ->count();

                if ($sentToday >= $dailyLimit) {
                    $isLimitReached = true;
                }
            }
            
            $pending = $total - $processed;
            
            if ($pending > 0 && $dailyLimit > 0) {
                $estimatedDays = ceil($pending / $dailyLimit);
                if ($estimatedDays <= 1) {
                    $etaHuman = "Should finish today";
                } else {
                    $etaHuman = "Estimated ~" . $estimatedDays . " days to complete";
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'status' => $status,
            'total' => $total,
            'processed' => $processed,
            'percent' => $percentage,
            'estimated_days' => $estimatedDays,
            'eta_human' => $etaHuman,
            'daily_limit' => $dailyLimit,
            'is_limit_reached' => $isLimitReached,
            'is_completed' => $status === 'COMPLETED' || $status === 'IMPORTED' || ($status === 'SENDING' && $percentage >= 100)
        ]);
    }





    public function store_campaign(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'template_id' => 'required',
            'lead_ids' => 'required|string',
            'schedule_type' => 'required|in:now,later',
            'schedule_time' => 'required_if:schedule_type,later|nullable',
        ]);

        $boxSlug = $request->route('box_slug');
        $box = Box::where('slug', $boxSlug)->first();

        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found.']);
        }

        $leadIds = explode(',', $request->lead_ids);
        
        $isQuickMessage = str_starts_with($request->campaign_name, 'Quick Message to ') || (count($leadIds) === 1 && $request->schedule_type === 'now');
        $campaign = null;

        if ($isQuickMessage && count($leadIds) === 1) {
            $lead = Lead::find($leadIds[0]);
            if ($lead) {
                $campaign = $this->getOrCreateDirectCampaign($box, $lead, 'Direct Custom Messages', $request->template_id);
                // Attach chatbot if provided
                if ($request->has('chatbot_id') && $request->chatbot_id) {
                    $campaign->whatsapp_chatbot_id = $request->chatbot_id;
                    $campaign->save();
                }
            }
        }

        if (!$campaign) {
            $campaign = WhatsAppCampaign::create([
                'box_id' => $box->id,
                'whats_app_template_id' => $request->template_id,
                'name' => $request->campaign_name,
                'schedule_at' => $request->schedule_type === 'later' ? $request->schedule_time : now(),
                'status' => 'PENDING',
                'whatsapp_chatbot_id' => $request->chatbot_id ?: null,
                'params' => [
                    'lead_ids' => $leadIds,
                    'schedule_type' => $request->schedule_type
                ],
                'added_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

        // If schedule_type is 'now', process it immediately
        if ($request->schedule_type === 'now') {
            try {
                $waHelper = new \App\Helpers\WhatsApp($box);
                $successCount = 0;
                $failCount = 0;
                $errors = [];

                foreach ($leadIds as $leadId) {
                    $message = WhatsAppMessage::create([
                        'box_id' => $box->id,
                        'whatsapp_campaign_id' => $campaign->id,
                        'lead_id' => $leadId,
                        'type' => 'OUTGOING',
                        'status' => 'PENDING',
                    ]);

                    $waHelper->sendTemplateMessage($message);
                    
                    if ($message->status === 'Sent') {
                        $successCount++;
                        // Log activity for this lead
                        $this->logWhatsAppActivity($box->id, $leadId, $campaign->template->name ?? 'WhatsApp Template', $message->text);
                    } else {
                        $failCount++;
                        $errors[] = "Lead {$leadId}: " . ($message->error ?: 'Unknown error');
                    }
                }
                
                $campaign->status = 'COMPLETED';
                $campaign->completed_at = now();
                $campaign->save();

                return response()->json([
                    'success' => true,
                    'message' => "Campaign \"{$campaign->name}\" sent instantly to {$successCount} leads" . ($failCount > 0 ? " ({$failCount} failed)." : "."),
                    'campaign' => $campaign,
                    'stats' => ['success' => $successCount, 'failed' => $failCount, 'errors' => $errors]
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Immediate campaign processing failed: " . $e->getMessage());
                // Leave it as PENDING so cron can retry
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign "' . $campaign->name . '" has been created and ' . ($request->schedule_type === 'later' ? 'scheduled.' : 'queued for sending.'),
            'campaign' => $campaign
        ]);
    }

    public function getChatSidebar(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false]);

        $tab = $request->get('tab', 'active');
        $offset = (int) $request->get('offset', 0);
        $limit = (int) $request->get('limit', 20);

        // Base query without counts (to keep it fast for aggregation)
        // Replaced GROUP BY derived table with a correlated JOIN for extreme performance
        $baseQuery = Lead::select('leads.*', 'wm.type as lm_type', 'wm.created_at as lm_date')
            ->join('whatsapp_messages as wm', function ($join) use ($box) {
                $join->on('wm.lead_id', '=', 'leads.id')
                     ->where('wm.box_id', '=', $box->id)
                     ->where('wm.id', '=', \Illuminate\Support\Facades\DB::raw("(SELECT id FROM whatsapp_messages sub WHERE sub.lead_id = leads.id AND sub.box_id = {$box->id} AND sub.deleted_at IS NULL ORDER BY created_at DESC, id DESC LIMIT 1)"));
            })
            ->where('leads.deleted_at', null);

        // Calculate counts quickly in a single query
        $countsSql = "
            SELECT 
                SUM(CASE WHEN lm_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN lm_type = 'OUTGOING' AND lm_date < DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN lm_type = 'INCOMING' AND lm_date < DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as notContacted
            FROM (" . $baseQuery->toSql() . ") as sub
        ";
        $countsObj = \Illuminate\Support\Facades\DB::selectOne($countsSql, $baseQuery->getBindings());

        // Fast count for unread using EXISTS
        $unreadCount = Lead::whereHas('whatsappMessages', function($q) use ($box) {
            $q->where('box_id', $box->id)->where('is_read', false)->where('type', 'INCOMING');
        })->count();

        $counts = [
            'unread' => $unreadCount,
            'active' => $countsObj->active ?? 0,
            'inactive' => $countsObj->inactive ?? 0,
            'notContacted' => $countsObj->notContacted ?? 0,
        ];

        // Apply Tab Filter
        $query = clone $baseQuery;
        if ($tab === 'unread') {
            // Unread filter needs a whereHas
            $query->whereHas('whatsappMessages', function($q) use ($box) {
                $q->where('box_id', $box->id)->where('is_read', false)->where('type', 'INCOMING');
            });
        } elseif ($tab === 'inactive') {
            $query->where('wm.type', 'OUTGOING')->where('wm.created_at', '<', now()->subHours(24));
        } elseif ($tab === 'notContacted') {
            $query->where('wm.type', 'INCOMING')->where('wm.created_at', '<', now()->subHours(24));
        } else {
            // Default to 'active'
            $query->where('wm.created_at', '>=', now()->subHours(24));
        }

        // Fetch Paginated Leads WITH relations and counts (only evaluates for the 20 results)
        $leads = $query->with(['whatsappChatSetting', 'latestWhatsAppMessage' => function($q) use ($box) {
                $q->where('box_id', $box->id)->with('campaign.template');
            }])
            ->withCount(['whatsappMessages as unread_count' => function($q) use ($box) {
                $q->where('box_id', $box->id)->where('is_read', false)->where('type', 'INCOMING');
            }])
            ->withCount(['whatsappMessages as pinned_count' => function($q) use ($box) {
                $q->where('whatsapp_messages.box_id', $box->id)
                  ->where('whatsapp_messages.is_pinned', true)
                  ->where(function($sq) {
                      $sq->whereNull('whatsapp_messages.pin_expires_at')
                        ->orWhere('whatsapp_messages.pin_expires_at', '>', now());
                  });
            }])
            ->orderBy('lm_date', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
            
        // Map to whatsappMessages relation collection for frontend compatibility
        foreach ($leads as $lead) {
            $lastMsg = $lead->latestWhatsAppMessage;
            $lead->setRelation('whatsappMessages', collect($lastMsg ? [$lastMsg] : []));
        }

        return response()->json([
            'success' => true,
            'leads' => $leads,
            'counts' => $counts,
            'hasMore' => count($leads) === $limit,
            'offset' => $offset + count($leads)
        ]);
    }
    public function sendTemplateDirect(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) {
            return response()->json(['success' => false, 'message' => 'Box not found']);
        }

        $leadId = $request->input('lead_id');
        $templateId = $request->input('template_id');

        $lead = Lead::find($leadId);
        $template = WhatsAppTemplate::find($templateId);

        if (!$lead || !$template) {
            return response()->json(['success' => false, 'message' => 'Lead or Template not found']);
        }

        // Get or reuse campaign for this template, checking the active chat thread first
        $campaign = $this->getOrCreateDirectCampaign($box, $lead, 'Direct Custom Messages', $template->id);

        $campaignNumber = \App\Models\WhatsAppCampaignNumber::create([
            'box_id' => $box->id,
            'whatsapp_campaign_id' => $campaign->id,
            'lead_id' => $lead->id,
            'phone' => $lead->phone,
            'name' => $lead->name,
            'status' => 'PENDING',
        ]);

        $whatsappMessage = WhatsAppMessage::create([
            'box_id' => $box->id,
            'lead_id' => $lead->id,
            'whatsapp_campaign_id' => $campaign->id,
            'type' => 'OUTGOING',
            'sender_id' => auth()->id(),
            'status' => 'Pending',
        ]);

        try {
            $whatsappHelper = new \App\Helpers\WhatsApp($box);
            $whatsappHelper->sendTemplateMessage($whatsappMessage);

            if (strtoupper($whatsappMessage->status) === 'SENT') {
                $campaignNumber->update(['status' => 'SENT']);
            } else {
                $campaignNumber->update(['status' => 'FAILED']);
            }

            // Log activity
            $this->logWhatsAppActivity($box->id, $leadId, $template->name, $whatsappMessage->text);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp template sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper to log WhatsApp communication as a CRM Activity
     */
    private function logWhatsAppActivity($boxId, $leadId, $title, $content = null)
    {
        try {
            $userId = auth()->id() ?? 1; // Fallback to 1 if system
            
            // Find the lead to determine who it is currently assigned to
            $lead = Lead::find($leadId);
            $assignedToId = $lead ? ($lead->assigned_to ?? $userId) : $userId;

            $timePrefix = "[" . now()->format('h:i A') . "]";
            $messageEntry = $timePrefix . " " . $title;
            if ($content) {
                $messageEntry .= ": " . $content;
            }

            // Check if there is an activity today for this lead
            $today = now()->format('Y-m-d');
            $existingActivity = Activity::where('box_id', $boxId)
                ->where('activity_id', $leadId)
                ->where('type', 'whatsapp')
                ->whereDate('activity_date', $today)
                ->first();

            if ($existingActivity) {
                // Append to existing activity description
                $newDescription = $existingActivity->description . "\n" . $messageEntry;
                $existingActivity->update([
                    'description' => $newDescription,
                    'last_updated_by' => $userId,
                ]);
            } else {
                // Create a new activity for the day
                $subject = "WhatsApp Conversation (" . now()->format('M d, Y') . ")";
                
                Activity::create([
                    'box_id'          => $boxId,
                    'activity_id'     => $leadId, 
                    'type'            => 'whatsapp',
                    'subject'         => $subject,
                    'description'     => $messageEntry,
                    'activity_date'   => now(),
                    'added_by'        => $userId,
                    'last_updated_by' => $userId,
                    'assigned_to'     => $assignedToId,
                    'status'          => 'Close',
                    'completed_date'  => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to log WhatsApp activity: " . $e->getMessage());
        }
    }
    public function clearChat(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false]);

        $leadId = $request->input('lead_id');
        if (!$leadId) return response()->json(['success' => false]);

        // Delete all messages for this lead in this box
        WhatsAppMessage::where('box_id', $box->id)
            ->where('lead_id', $leadId)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function deleteChat(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false]);

        $leadId = $request->input('lead_id');
        if (!$leadId) return response()->json(['success' => false]);

        // 1. Clear messages
        $this->clearChat($request, $boxSlug);

        // 2. We could archive the lead or mark it as deleted specifically for WhatsApp
        // For now, clearing messages is sufficient to "delete" the chat history.
        
        return response()->json(['success' => true]);
    }

    public function blockUser(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false]);

        $leadId = $request->input('lead_id');
        if (!$leadId) return response()->json(['success' => false]);

        $lead = Lead::find($leadId);
        if ($lead) {
            // Update lead status or add to a blocked list if table exists
            // For now, we'll just delete the chat
            $this->deleteChat($request, $boxSlug);
        }

        return response()->json(['success' => true]);
    }
    public function updateChatSettings(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        if (!$box) return response()->json(['success' => false]);

        $leadId = $request->input('lead_id');
        if (!$leadId) return response()->json(['success' => false]);

        $type = $request->input('type'); // 'disappearing' or 'mute'
        $value = $request->input('value');

        $setting = WhatsAppChatSetting::firstOrNew([
            'box_id' => $box->id,
            'lead_id' => $leadId
        ]);

        $userId = auth()->id();
        if (!$setting->exists) {
            $setting->added_by = $userId;
        }
        $setting->last_updated_by = $userId;

        if ($type === 'disappearing') {
            $setting->disappearing_duration = ($value === 'off') ? null : $value;
        } elseif ($type === 'mute') {
            if ($value === 'always') {
                $setting->muted_until = now()->addYears(10);
            } elseif ($value === '8_hours') {
                $setting->muted_until = now()->addHours(8);
            } elseif ($value === '1_week') {
                $setting->muted_until = now()->addWeek();
            } else {
                $setting->muted_until = null;
            }
        }

        $setting->save();

        return response()->json(['success' => true]);
    }
    public function previewExcelCampaign(Request $request, $boxSlug)
    {
        $request->validate([
            'excel_file' => 'required|file|max:51200|mimes:xlsx,xls,csv,txt'
        ]);

        try {
            $file = $request->file('excel_file');
            if (!$file) {
                return response()->json(['success' => false, 'message' => 'No file uploaded. Check your PHP upload_max_filesize and post_max_size settings.']);
            }

            $filePath = $file->getRealPath();
            \Log::info('Previewing Excel: ' . $file->getClientOriginalName() . ' size: ' . $file->getSize());
            
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

            // Get actual total rows first from sheet metadata so the filter doesn't cap it at 100
            $totalRows = 0;
            try {
                $worksheetInfo = $reader->listWorksheetInfo($filePath);
                if (!empty($worksheetInfo) && isset($worksheetInfo[0]['totalRows'])) {
                    $totalRows = $worksheetInfo[0]['totalRows'];
                }
            } catch (\Exception $infoEx) {
                // Fallback handled below
            }

            // Use a ReadFilter to only read the first 100 rows for preview
            $filter = new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                public function readCell($columnAddress, $row, $worksheetName = '') {
                    return $row <= 100;
                }
            };

            $reader->setReadFilter($filter);
            $reader->setReadDataOnly(true);
            
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows)) {
                return response()->json(['success' => false, 'message' => 'The file is empty or could not be read.']);
            }

            // Get headers (first row) and sample data
            $headers = $rows[0];
            $samples = array_slice($rows, 0, 5); 

            // If listWorksheetInfo did not get the count, fallback to loaded highest row
            if ($totalRows <= 0) {
                $totalRows = $worksheet->getHighestRow();
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            return response()->json([
                'success' => true,
                'headers' => $headers,
                'samples' => $samples,
                'total_rows' => $totalRows
            ]);
        } catch (\Exception $e) {
            \Log::error('Preview Excel Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Preview failed: ' . $e->getMessage()]);
        }
    }

    public function showCampaign(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::with(['template'])->where('box_id', $box->id)->findOrFail($id);
        
        // Extract variables from template for dynamic columns
        $variables = [];
        $template = $campaign->template;
        if ($template && $template->components && is_array($template->components)) {
            foreach ($template->components as $component) {
                if ($component['type'] === 'BODY' && isset($component['text'])) {
                    preg_match_all('/{{(\d+)}}/', $component['text'], $matches);
                    if (!empty($matches[1])) {
                        $variables = array_unique($matches[1]);
                        sort($variables);
                    }
                }
            }
        }
        $vars = array_values($variables);

        return view('whatsapp.campaign_details', compact('box', 'campaign', 'vars'));
    }

    public function updateCampaign(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);

        if (!in_array($campaign->status, ['PENDING', 'SCHEDULED'])) {
            return response()->json(['success' => false, 'message' => 'Only scheduled or pending campaigns can be edited.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'whats_app_template_id' => 'required|exists:whats_app_templates,id',
            'send_type' => 'required|in:now,scheduled',
            'schedule_at' => 'required_if:send_type,scheduled|nullable|date',
        ]);

        $campaign->name = $request->name;
        $campaign->whats_app_template_id = $request->whats_app_template_id;
        $campaign->whatsapp_chatbot_id = $request->chatbot_id ?: null;
        
        if ($request->send_type === 'now') {
            $campaign->schedule_at = null;
            $campaign->status = 'PENDING';
        } else {
            $campaign->schedule_at = $request->schedule_at ? \Carbon\Carbon::parse($request->schedule_at) : null;
            $campaign->status = 'SCHEDULED';
        }

        $campaign->save();

        return response()->json(['success' => true, 'message' => 'Campaign updated successfully!']);
    }

    public function getCampaignContactsData(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::with('template')->findOrFail($id);
        
        // Extract variables from template for dynamic columns
        $variables = [];
        $template = $campaign->template;
        if ($template && $template->components && is_array($template->components)) {
            foreach ($template->components as $component) {
                if ($component['type'] === 'BODY' && isset($component['text'])) {
                    preg_match_all('/{{(\d+)}}/', $component['text'], $matches);
                    if (!empty($matches[1])) {
                        $variables = array_unique($matches[1]);
                        sort($variables);
                    }
                }
            }
        }
        $vars = array_values($variables);

        $query = WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)
            ->where('box_id', $box->id);

        // Apply status/tab filters
        $statusFilter = $request->input('status');
        if ($statusFilter) {
            $statusFilter = strtolower($statusFilter);
            if ($statusFilter === 'sent') {
                $query->where('status', 'SENT');
            } elseif ($statusFilter === 'failed') {
                $query->where('status', 'FAILED');
            } elseif ($statusFilter === 'pending') {
                $query->where('status', 'PENDING');
            } elseif ($statusFilter === 'delivered') {
                $query->where('status', 'SENT');
            } elseif ($statusFilter === 'read') {
                $query->where(function($q) use ($id) {
                    $q->whereIn('lead_id', function($sub) use ($id) {
                        $sub->select('lead_id')
                            ->from('whatsapp_messages')
                            ->where('whatsapp_campaign_id', $id)
                            ->where('is_read', true)
                            ->whereNotNull('lead_id');
                    })
                    ->orWhereIn('phone', function($sub) use ($id) {
                        $sub->select('leads.phone')
                            ->from('whatsapp_messages')
                            ->join('leads', 'whatsapp_messages.lead_id', '=', 'leads.id')
                            ->where('whatsapp_messages.whatsapp_campaign_id', $id)
                            ->where('whatsapp_messages.is_read', true);
                    });
                });
            } elseif ($statusFilter === 'replied') {
                $query->where(function($q) {
                    $q->whereIn('lead_id', function($sub) {
                        $sub->select('lead_id')
                            ->from('whatsapp_messages')
                            ->where('type', 'INCOMING')
                            ->whereNotNull('lead_id');
                    })
                    ->orWhereIn('phone', function($sub) {
                        $sub->select('leads.phone')
                            ->from('whatsapp_messages')
                            ->join('leads', 'whatsapp_messages.lead_id', '=', 'leads.id')
                            ->where('whatsapp_messages.type', 'INCOMING');
                    });
                });
            } elseif ($statusFilter === 'clicked') {
                $query->whereRaw('1 = 0');
            }
        }

        $isLimitReached = false;
        if ($campaign->status === 'SENDING') {
            $whatsappNumber = WhatsAppPhoneNumber::where('box_id', $box->id)->where('is_active', 1)->first();
            if ($whatsappNumber) {
                $dailyLimit = $whatsappNumber->metadata['daily_limit'] ?? 1000;
                $sentToday = \App\Models\WhatsAppMessage::where('box_id', $box->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->where('status', '!=', 'Failed')
                    ->count();

                if ($sentToday >= $dailyLimit) {
                    $isLimitReached = true;
                }
            }
        }

        $dt = \Yajra\DataTables\Facades\DataTables::of($query)
            ->with([
                'is_limit_reached' => $isLimitReached,
                'is_outside_window' => false,
                'total_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->count(),
                'sent_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->where('status', 'SENT')->count(),
                'pending_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->where('status', 'PENDING')->count(),
                'failed_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->where('status', 'FAILED')->count(),
                'delivered_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)->where('status', 'SENT')->count(),
                'read_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)
                    ->where(function($q) use ($id) {
                        $q->whereIn('lead_id', function($sub) use ($id) {
                            $sub->select('lead_id')
                                ->from('whatsapp_messages')
                                ->where('whatsapp_campaign_id', $id)
                                ->where('is_read', true)
                                ->whereNotNull('lead_id');
                        })
                        ->orWhereIn('phone', function($sub) use ($id) {
                            $sub->select('leads.phone')
                                ->from('whatsapp_messages')
                                ->join('leads', 'whatsapp_messages.lead_id', '=', 'leads.id')
                                ->where('whatsapp_messages.whatsapp_campaign_id', $id)
                                ->where('whatsapp_messages.is_read', true);
                        });
                    })->count(),
                'replied_count' => WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)
                    ->where(function($q) {
                        $q->whereIn('lead_id', function($sub) {
                            $sub->select('lead_id')
                                ->from('whatsapp_messages')
                                ->where('type', 'INCOMING')
                                ->whereNotNull('lead_id');
                        })
                        ->orWhereIn('phone', function($sub) {
                            $sub->select('leads.phone')
                                ->from('whatsapp_messages')
                                ->join('leads', 'whatsapp_messages.lead_id', '=', 'leads.id')
                                ->where('whatsapp_messages.type', 'INCOMING');
                        });
                    })->count(),
                'clicked_count' => 0, // Clicked tracking can be a future feature, for now default to 0
            ])
            ->addColumn('checkbox', function($row) {
                if ($row->lead_id) {
                    return '<i class="bx bx-check-shield text-success fs-5" title="Already a Lead"></i>';
                }
                return '<input class="recipient-checkbox" type="checkbox" value="'.$row->id.'" style="width: 17px; height: 17px;">';
            })
            ->editColumn('recipient', function($row) {
                $avatar = strtoupper(substr($row->name ?? $row->phone, 0, 1));
                $bg = $row->lead_id ? 'success' : 'primary';
                $badge = $row->lead_id ? '<span class="badge bg-soft-success text-success mt-1" style="font-size: 9px;">CRM LEAD</span>' : '';
                
                return '
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm me-3 bg-'.$bg.' bg-opacity-10 text-'.$bg.' rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px;">
                            '.$avatar.'
                        </div>
                        <div>
                            <div class="fw-bold text-dark small mb-0">'.($row->name ?? 'Contact').'</div>
                            <div class="text-muted small">+'.$row->phone.'</div>
                            '.$badge.'
                        </div>
                    </div>';
            })
            ->editColumn('status', function($row) {
                if ($row->status === 'SENT') {
                    return '<span class="badge bg-soft-success text-success rounded-pill px-3 py-1">Delivered</span>';
                } elseif ($row->status === 'FAILED') {
                    return '<span class="badge bg-soft-danger text-danger rounded-pill px-3 py-1">Failed</span>';
                }
                return '<span class="badge bg-soft-warning text-warning rounded-pill px-3 py-1">Pending</span>';
            })
            ->addColumn('action', function($row) {
                if ($row->lead_id) {
                    return '<a href="'.url('/leads/'.$row->lead_id).'" class="btn btn-sm btn-light border p-2"><i class="bx bx-user"></i></a>';
                }
                return '<button class="btn btn-sm btn-soft-primary border p-2 btn-convert-lead" data-id="'.$row->id.'"><i class="bx bx-user-plus"></i></button>';
            });

        // Add dynamic columns for variables
        foreach ($vars as $v) {
            $dt->addColumn('var_'.$v, function($row) use ($v) {
                return $row->metadata['variables'][$v] ?? '-';
            });
        }

        return $dt->rawColumns(['checkbox', 'recipient', 'status', 'action'])
            ->make(true);
    }

    public function startSendingCampaign(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);

        if ($campaign->status === 'COMPLETED') {
            return response()->json(['success' => false, 'message' => 'Campaign already completed.']);
        }

        // Update status to SENDING so the job processes it
        $campaign->update(['status' => 'SENDING']);

        // Dispatch the background job immediately to execute the campaign
        \App\Jobs\SendWhatsAppCampaignJob::dispatch($campaign);

        return response()->json([
            'success' => true,
            'message' => "Campaign started. Messages are being processed and logged immediately."
        ]);
    }

    public function changeCampaignStatus(Request $request, $boxSlug, $id)
    {
        $request->validate([
            'status' => 'required|string|in:PAUSED,STOPPED,SENDING'
        ]);

        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);
        $status = $request->input('status');

        if ($campaign->status === 'COMPLETED' || $campaign->status === 'FAILED' || $campaign->status === 'STOPPED') {
            return response()->json(['success' => false, 'message' => 'Cannot change status of a completed, stopped, or failed campaign.']);
        }

        $campaign->update(['status' => $status]);

        if ($status === 'SENDING') {
            // Resume sending
            \App\Jobs\SendWhatsAppCampaignJob::dispatch($campaign);
            $message = "Campaign resumed successfully.";
        } elseif ($status === 'PAUSED') {
            $message = "Campaign paused successfully.";
        } else {
            $message = "Campaign stopped successfully.";
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function exportCampaignExcel(Request $request, $boxSlug)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $status = $request->query('status');
        
        $fileName = 'campaigns_' . time() . '.xlsx';
        return Excel::download(new \App\Exports\WhatsAppCampaignExport($box->id, $status), $fileName);
    }

    public function exportCampaignPDF(Request $request, $boxSlug)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $status = $request->query('status');
        
        $query = WhatsAppCampaign::with('template')->withCount('messages')->where('box_id', $box->id);
        if ($status) {
            if ($status === 'COMPLETED') {
                $query->whereIn('status', ['COMPLETED', 'FAILED']);
            } else {
                $query->where('status', $status);
            }
        }
        $campaigns = $query->get();
        
        $pdf = Pdf::loadView('whatsapp.exports.campaigns_pdf', compact('campaigns'));
        return $pdf->download('campaigns_' . time() . '.pdf');
    }

    public function exportCampaignDetails(Request $request, $boxSlug, $id)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(300);
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $format = $request->query('format', 'excel');
        
        $count = \App\Models\WhatsAppCampaignNumber::where('whatsapp_campaign_id', $id)
            ->where('box_id', $box->id)
            ->count();
            
        // Calculate estimated time
        // Excel handles ~2000 rows per second, PDF handles ~100 rows per second due to DOM rendering
        if ($format === 'pdf') {
            $seconds = ceil($count / 100) + 5; 
        } else {
            $seconds = ceil($count / 2000) + 2;
        }
        
        $timeString = $seconds < 60 ? "{$seconds} seconds" : ceil($seconds / 60) . " minutes";
        
        \App\Jobs\ExportWhatsAppCampaignDetailsJob::dispatch($id, $box->id, auth()->id(), $format);
        
        return response()->json([
            'success' => true,
            'message' => "Export job started in background. Estimated time: {$timeString}. You will receive an email shortly."
        ]);
    }

    public function getTemplateVariables(Request $request, $boxSlug, $id)
    {
        $template = WhatsAppTemplate::findOrFail($id);
        $variables = [];
        $previewHtml = '<div class="whatsapp-template-preview" style="background:#e5ddd5; padding:10px; border-radius:8px; font-size:13px; font-family:sans-serif; max-width:100%; border: 1px solid #ddd;">';
        $previewHtml .= '<div style="background:#fff; padding:8px 12px; border-radius:8px; display:inline-block; max-width:100%; box-shadow:0 1px 1px rgba(0,0,0,0.1); width: 100%;">';
        
        if ($template->components && is_array($template->components)) {
            foreach ($template->components as $component) {
                if ($component['type'] === 'HEADER') {
                    if (isset($component['text'])) {
                        $previewHtml .= '<div style="font-weight:bold; margin-bottom:5px; font-size:14px; color:#000;">' . nl2br(htmlspecialchars($component['text'])) . '</div>';
                    } elseif (isset($component['format']) && in_array($component['format'], ['IMAGE', 'VIDEO', 'DOCUMENT'])) {
                        $iconClass = strtolower($component['format']) == 'image' ? 'bx-image' : (strtolower($component['format']) == 'video' ? 'bx-video' : 'bx-file');
                        $previewHtml .= '<div style="background:#f0f0f0; padding:15px; text-align:center; border-radius:5px; margin-bottom:8px;"><i class="bx ' . $iconClass . ' fs-2 text-muted"></i><div class="small text-muted mt-1">' . ucfirst(strtolower($component['format'])) . ' Header</div></div>';
                    }
                }
                if ($component['type'] === 'BODY' && isset($component['text'])) {
                    $text = htmlspecialchars($component['text']);
                    $text = preg_replace('/{{(\d+)}}/', '<span class="badge bg-primary px-1 rounded-pill" style="font-size: 11px;">{{$1}}</span>', $text);
                    $previewHtml .= '<div style="margin-bottom:5px; color:#111; line-height: 1.4;">' . nl2br($text) . '</div>';
                    
                    preg_match_all('/{{(\d+)}}/', $component['text'], $matches);
                    if (!empty($matches[1])) {
                        $variables = array_unique($matches[1]);
                        sort($variables);
                    }
                }
                if ($component['type'] === 'FOOTER' && isset($component['text'])) {
                    $previewHtml .= '<div style="color:#888; font-size:11px; margin-top:5px;">' . nl2br(htmlspecialchars($component['text'])) . '</div>';
                }
            }
            
            $buttonsHtml = '';
            foreach ($template->components as $component) {
                if ($component['type'] === 'BUTTONS' && isset($component['buttons'])) {
                    foreach ($component['buttons'] as $btn) {
                        $icon = '';
                        if (isset($btn['type'])) {
                            if ($btn['type'] == 'URL') $icon = '<i class="bx bx-link-external me-1"></i>';
                            if ($btn['type'] == 'PHONE_NUMBER') $icon = '<i class="bx bxs-phone me-1"></i>';
                        }
                        $btnText = htmlspecialchars($btn['text'] ?? 'Button');
                        $buttonsHtml .= '<div style="margin-top:8px; border-top:1px solid #f0f0f0; padding-top:8px; text-align:center; color:#00a884; font-weight:500;">' . $icon . $btnText . '</div>';
                    }
                }
            }
            $previewHtml .= $buttonsHtml;
        }
        
        $previewHtml .= '</div></div>';

        return response()->json([
            'success' => true,
            'variables' => array_values($variables),
            'template_name' => $template->name,
            'preview_html' => $previewHtml
        ]);
    }

    public function convertChatToLead(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $lead = Lead::where('box_id', $box->id)->findOrFail($id);

        $assignedTo = $request->input('assigned_to') ?: auth()->id();

        // Check if this contact is already a lead (e.g. assigned to someone or already has a status/source)
        if ($lead->assigned_to !== null) {
            if ($lead->assigned_to == $assignedTo) {
                $assignedUser = $lead->assignedTo ? $lead->assignedTo->name : 'another user';
                return response()->json([
                    'success' => false,
                    'message' => 'This contact is already registered as a lead, assigned to ' . $assignedUser . '.'
                ], 422);
            } else {
                // Reassign the lead
                $oldAssignedUser = $lead->assignedTo ? $lead->assignedTo->name : 'Unassigned';
                $lead->assigned_to = $assignedTo;
                $lead->save();

                // Create Reassignment Activity Log
                Activity::create([
                    'box_id' => $box->id,
                    'activity_id' => $lead->id,
                    'activity_type' => 'Lead',
                    'subject' => 'Reassigned Lead',
                    'description' => 'Reassigned from ' . $oldAssignedUser . ' by ' . auth()->user()->name,
                    'type' => 'Note',
                    'activity_date' => now(),
                    'added_by' => auth()->id(),
                    'assigned_to' => $assignedTo,
                    'status' => 'Close',
                    'last_updated_by' => auth()->id()
                ]);

                // Send Notification to new user
                try {
                    $newUser = User::find($assignedTo);
                    if ($newUser) {
                        $newUser->notify(new \App\Notifications\NewLeadNotification($lead, 'assigned', null, 'WhatsApp Chat Reassignment'));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send notification for reassigned chat lead: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully reassigned Lead!',
                    'lead' => $lead->load(['leadStatus', 'leadSource', 'assignedTo'])
                ]);
            }
        }

        // Update the lead fields to "convert" it
        $lead->assigned_to = $assignedTo;
        
        // Find a default ContactStage (lead status) for this box
        $firstStatus = ContactStage::where('box_id', $box->id)->orderBy('id', 'asc')->first();
        if ($firstStatus) {
            $lead->lead_status_id = $firstStatus->id;
        }

        // Set lead source if not set
        if (!$lead->lead_source_id) {
            $source = \App\Models\CrmSource::where('box_id', $box->id)->where('name', 'WhatsApp')->first()
                ?? \App\Models\CrmSource::where('box_id', $box->id)->first()
                ?? \App\Models\CrmSource::where('name', 'WhatsApp')->first()
                ?? \App\Models\CrmSource::first();
            if ($source) {
                $lead->lead_source_id = $source->id;
            }
        }

        $lead->save();

        // Create Lead Activity Log
        Activity::create([
            'box_id' => $box->id,
            'activity_id' => $lead->id,
            'activity_type' => 'Lead',
            'subject' => 'Converted to Lead',
            'description' => 'Converted from WhatsApp Chat by ' . auth()->user()->name,
            'type' => 'Note',
            'activity_date' => now(),
            'added_by' => auth()->id(),
            'assigned_to' => $assignedTo,
            'status' => 'Close',
            'last_updated_by' => auth()->id()
        ]);

        // Send Notification if applicable
        try {
            $user = User::find($assignedTo); // Notify the assigned user, not the one making the action
            if ($user) {
                $user->notify(new \App\Notifications\NewLeadNotification($lead, 'assigned', null, 'WhatsApp Chat Conversion'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification for converted chat lead: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully converted to Lead!',
            'lead' => $lead->load(['leadStatus', 'leadSource', 'assignedTo'])
        ]);
    }

    public function convertToLead(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $number = WhatsAppCampaignNumber::with('campaign')->where('box_id', $box->id)->findOrFail($id);

        if ($number->lead_id) {
            return response()->json(['success' => false, 'message' => 'Lead already exists.']);
        }

        $lead = Lead::updateOrCreate(
            ['box_id' => $box->id, 'phone' => $number->phone],
            [
                'name' => $number->name ?? ('Imported ' . $number->phone),
                'lead_status_id' => 1,
                'added_by' => auth()->id(),
                'assigned_to' => auth()->id(),
                'created_from' => 'WhatsApp Campaign'
            ]
        );

        // Create Activity Log
        Activity::create([
            'box_id' => $box->id,
            'activity_id' => $lead->id,
            'activity_type' => 'Lead',
            'subject' => 'Converted from WhatsApp',
            'description' => 'Converted from Campaign: ' . ($number->campaign->name ?? 'N/A'),
            'type' => 'Note',
            'activity_date' => now(),
            'added_by' => auth()->id(),
            'assigned_to' => auth()->id(),
            'status' => 'Close',
            'last_updated_by' => auth()->id()
        ]);

        $number->update(['lead_id' => $lead->id]);

        // Send Notification
        try {
            $user = auth()->user();
            if ($user) {
                $user->notify(new \App\Notifications\NewLeadNotification($lead, 'assigned', null, 'WhatsApp: ' . ($number->campaign->name ?? 'Campaign')));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send notification for converted lead: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead created, assigned, and notified successfully.',
            'lead_id' => $lead->id,
            'lead_url' => url('/leads/' . $lead->id)
        ]);
    }

    public function saveCustomParams(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $params = $box->params ?? [];
        $params['custom_app_id'] = $request->input('custom_app_id');
        $params['custom_app_secret'] = $request->input('custom_app_secret');
        $box->params = $params;
        $box->save();

        return response()->json(['success' => true]);
    }

    public function generateApiToken(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $box->whatsapp_api_token = \Illuminate\Support\Str::random(60);
        $box->save();

        return response()->json([
            'success' => true,
            'token' => $box->whatsapp_api_token,
            'message' => 'API Token generated successfully.'
        ]);
    }

    public function saveWebhookUrl(Request $request, $boxSlug)
    {
        $request->validate(['webhook_url' => 'nullable|url']);
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $box->whatsapp_webhook_url = $request->webhook_url;
        $box->save();

        return response()->json([
            'success' => true,
            'message' => 'Webhook URL saved successfully.'
        ]);
    }

    public function getRecentIncomingMessages(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $messages = \App\Models\WhatsAppMessage::with('lead')
            ->where('box_id', $box->id)
            ->where('type', 'INCOMING')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($msg) {
                return [
                    'message_id' => $msg->message_id,
                    'text' => $msg->text,
                    'media' => $msg->media,
                    'sender' => $msg->lead ? $msg->lead->phone : 'Unknown',
                    'received_at' => $msg->created_at->diffForHumans(),
                    'raw_json_simulated' => [
                        'object' => 'whatsapp_business_account',
                        'entry' => [
                            [
                                'id' => '...',
                                'changes' => [
                                    [
                                        'value' => [
                                            'messages' => [
                                                [
                                                    'from' => $msg->lead ? $msg->lead->phone : 'Unknown',
                                                    'id' => $msg->message_id,
                                                    'timestamp' => $msg->created_at->timestamp,
                                                    'text' => ['body' => $msg->text],
                                                    'type' => $msg->media ? 'media' : 'text'
                                                ]
                                            ]
                                        ],
                                        'field' => 'messages'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function getApiTemplates(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        
        $templates = \App\Models\WhatsAppTemplate::where('box_id', $box->id)
            ->where('status', 'APPROVED')
            ->get();

        $chatbots = \App\Models\WhatsAppChatbot::where('box_id', $box->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        $apiTemplates = $templates->map(function($template) {
            $variables = [];
            $components = $template->components;
            if (is_array($components)) {
                foreach ($components as $component) {
                    if (isset($component['text']) && ($component['type'] === 'BODY' || $component['type'] === 'HEADER')) {
                        preg_match_all('/\{\{(\d+)\}\}/', $component['text'], $matches);
                        if (!empty($matches[1])) {
                            foreach ($matches[1] as $var) {
                                $variables[] = (int)$var;
                            }
                        }
                    }
                }
            }
            $variables = array_unique($variables);
            sort($variables);

            $apiConfig = $template->params['api_config'] ?? ['aliases' => [], 'chatbot_id' => null];

            return [
                'id' => $template->id,
                'name' => $template->name,
                'category' => $template->category,
                'language' => $template->language,
                'variables' => $variables,
                'api_config' => $apiConfig
            ];
        });

        return response()->json([
            'success' => true,
            'templates' => $apiTemplates,
            'chatbots' => $chatbots
        ]);
    }

    public function saveApiTemplateConfig(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $template = \App\Models\WhatsAppTemplate::where('box_id', $box->id)->findOrFail($id);

        $params = $template->params ?? [];
        $params['api_config'] = [
            'aliases' => $request->aliases ?? [],
            'chatbot_id' => $request->chatbot_id ?? null
        ];

        $template->params = $params;
        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'API Configuration saved successfully!'
        ]);
    }

    public function bulkConvertToLead(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $ids = $request->input('ids', []);
        $allMode = $request->boolean('all_mode');
        $campaignId = $request->input('campaign_id');
        
        if (empty($ids) && !$allMode) {
            return response()->json(['success' => false, 'message' => 'No recipients selected.']);
        }

        $query = WhatsAppCampaignNumber::with('campaign')
            ->where('box_id', $box->id)
            ->whereNull('lead_id');

        if ($allMode && $campaignId) {
            $query->where('whatsapp_campaign_id', $campaignId);
        } else {
            $query->whereIn('id', $ids);
        }

        $numbers = $query->get();

        if ($numbers->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'All selected recipients are already registered as CRM leads.',
                'processed_ids' => []
            ]);
        }

        $processedCount = 0;
        $processedIds = [];
        foreach ($numbers as $number) {
            $lead = Lead::updateOrCreate(
                ['box_id' => $box->id, 'phone' => $number->phone],
                [
                    'name' => $number->name ?? ('Imported ' . $number->phone),
                    'lead_status_id' => 1,
                    'added_by' => auth()->id(),
                    'assigned_to' => auth()->id(),
                    'created_from' => 'WhatsApp Campaign (Bulk)'
                ]
            );

            Activity::create([
                'box_id' => $box->id,
                'activity_id' => $lead->id,
                'activity_type' => 'Lead',
                'subject' => 'Converted from WhatsApp (Bulk)',
                'description' => 'Converted from Campaign: ' . ($number->campaign->name ?? 'N/A'),
                'type' => 'Note',
                'activity_date' => now(),
                'added_by' => auth()->id(),
                'assigned_to' => auth()->id(),
                'status' => 'Close',
                'last_updated_by' => auth()->id()
            ]);

            $number->update(['lead_id' => $lead->id]);
            $processedIds[] = $number->id;
            
            // Send Notification
            try {
                auth()->user()->notify(new \App\Notifications\NewLeadNotification($lead, 'assigned', null, 'WhatsApp Bulk: ' . ($number->campaign->name ?? 'Campaign')));
            } catch (\Exception $e) {}

            $processedCount++;
        }

        return response()->json([
            'success' => true, 
            'message' => "Successfully converted {$processedCount} recipients to leads.",
            'processed_ids' => $processedIds
        ]);
    }

    public function destroyCampaign(Request $request, $boxSlug, $id)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $campaign = WhatsAppCampaign::where('box_id', $box->id)->findOrFail($id);

        try {
            // Delete associated numbers/recipients
            WhatsAppCampaignNumber::where('whatsapp_campaign_id', $campaign->id)->delete();
            
            // Delete the campaign
            $campaign->delete();

            return response()->json([
                'success' => true,
                'message' => 'Campaign deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete campaign.'
            ], 500);
        }
    }

    public function bulkDestroyCampaign(Request $request, $boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->firstOrFail();
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No campaigns selected for deletion.'
            ], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Fetch campaigns belonging to this box
            $campaigns = WhatsAppCampaign::where('box_id', $box->id)
                ->whereIn('id', $ids)
                ->get();

            if ($campaigns->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching campaigns found.'
                ], 404);
            }

            $campaignIds = $campaigns->pluck('id');

            // Delete associated numbers/recipients
            WhatsAppCampaignNumber::whereIn('whatsapp_campaign_id', $campaignIds)->delete();

            // Delete the campaigns
            WhatsAppCampaign::whereIn('id', $campaignIds)->delete();

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($campaignIds) . ' campaign(s) deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk delete campaigns: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBusinessProfile(Request $request)
    {
        $phoneId = $request->input('phone_number_id');
        if (!$phoneId) {
            return response()->json(['success' => false, 'error' => 'Phone number ID is required.']);
        }
        
        $phone = WhatsAppPhoneNumber::where('whatsapp_id', $phoneId)->first();
        if (!$phone) {
            return response()->json(['success' => false, 'error' => 'Phone number not found in your account.']);
        }
        
        $box = Box::find($phone->box_id);
        if (!$box || empty($box->whatsapp_token['access_token'])) {
            return response()->json(['success' => false, 'error' => 'WhatsApp token not found.']);
        }

        $token = $box->whatsapp_token['access_token'];
        $url = "https://graph.facebook.com/v22.0/{$phoneId}/whatsapp_business_profile?fields=about,address,description,email,profile_picture_url,websites,vertical";
        
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)->get($url);
            $data = $response->json();
            
            if ($response->successful() && isset($data['data'][0])) {
                return response()->json([
                    'success' => true,
                    'data' => $data['data'][0]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $data['error']['message'] ?? 'Failed to fetch profile from Meta.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updateBusinessProfile(Request $request)
    {
        $phoneId = $request->input('phone_number_id');
        if (!$phoneId) {
            return response()->json(['success' => false, 'error' => 'Phone number ID is required.']);
        }
        
        $phone = WhatsAppPhoneNumber::where('whatsapp_id', $phoneId)->first();
        if (!$phone) {
            return response()->json(['success' => false, 'error' => 'Phone number not found in your account.']);
        }
        
        $box = Box::find($phone->box_id);
        if (!$box || empty($box->whatsapp_token['access_token'])) {
            return response()->json(['success' => false, 'error' => 'WhatsApp token not found.']);
        }

        $token = $box->whatsapp_token['access_token'];
        $url = "https://graph.facebook.com/v22.0/{$phoneId}/whatsapp_business_profile";
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'description' => $request->input('description', ''),
            'address' => $request->input('address', ''),
            'email' => $request->input('email', ''),
            'vertical' => $request->input('vertical', 'UNDEF'),
            'about' => $request->input('about', '')
        ];
        
        if ($request->has('websites')) {
            $payload['websites'] = $request->input('websites');
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)->post($url, $payload);
            $data = $response->json();
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $data['error']['message'] ?? 'Failed to update profile.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getOrCreateDirectCampaign($box, $lead, $defaultName = 'Direct Custom Messages', $templateId = null)
    {
        // 1. Try to find the latest campaign associated with this chat thread
        $latestMsg = WhatsAppMessage::where('lead_id', $lead->id)
            ->whereNotNull('whatsapp_campaign_id')
            ->latest()
            ->first();

        if ($latestMsg) {
            $campaign = WhatsAppCampaign::find($latestMsg->whatsapp_campaign_id);
            if ($campaign) {
                // If we are sending a specific template, DO NOT reuse a campaign that is for a different template
                $canReuse = false;
                if ($templateId && $campaign->whats_app_template_id == $templateId) {
                    $canReuse = true; // Same template, safe to group
                } elseif (!$templateId && !$campaign->whats_app_template_id) {
                    $canReuse = true; // Both are free-form direct messages, safe to group
                }

                if ($canReuse) {
                    $campaign->increment('total_rows');
                    $campaign->increment('processed_rows');
                    return $campaign;
                }
            }
        }

        // 2. If it's a template, try to find an existing Quick Message campaign for this template
        if ($templateId) {
            $template = WhatsAppTemplate::find($templateId);
            if ($template) {
                $campaign = WhatsAppCampaign::firstOrCreate(
                    [
                        'box_id' => $box->id,
                        'name' => 'Quick Message: ' . $template->name,
                        'whats_app_template_id' => $template->id,
                    ],
                    [
                        'status' => 'COMPLETED',
                        'schedule_at' => now(),
                        'total_rows' => 0,
                        'processed_rows' => 0,
                    ]
                );
                $campaign->increment('total_rows');
                $campaign->increment('processed_rows');
                return $campaign;
            }
        }

        // 3. Fallback to Direct Custom Messages campaign
        $campaign = WhatsAppCampaign::firstOrCreate(
            [
                'box_id' => $box->id,
                'name' => $defaultName,
            ],
            [
                'status' => 'COMPLETED',
                'schedule_at' => now(),
                'total_rows' => 0,
                'processed_rows' => 0,
            ]
        );
        $campaign->increment('total_rows');
        $campaign->increment('processed_rows');
        return $campaign;
    }
}
