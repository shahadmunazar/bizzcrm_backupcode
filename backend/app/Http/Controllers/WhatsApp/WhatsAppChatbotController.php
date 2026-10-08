<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppChatbot;
use App\Models\Box;
use Illuminate\Http\Request;

class WhatsAppChatbotController extends Controller
{
    private function getBoxId($boxSlug)
    {
        $box = Box::where('slug', $boxSlug)->first();
        return $box ? $box->id : null;
    }

    public function index(Request $request, $boxSlug)
    {
        $boxId = $this->getBoxId($boxSlug);
        
        if ($request->ajax()) {
            $query = WhatsAppChatbot::where('box_id', $boxId)->with('project');
            
            if ($request->filled('project_id')) {
                $query->where('whatsapp_project_id', $request->project_id);
            }
            
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->addColumn('project_name', function($row) {
                    return $row->project ? $row->project->name : 'Global';
                })
                ->addColumn('trigger_badge', function($row) {
                    return '<span class="badge bg-info">' . ucfirst(str_replace('_', ' ', $row->trigger_type)) . '</span>';
                })
                ->addColumn('status_badge', function($row) {
                    if ($row->is_active) {
                        return '<span class="badge bg-success">Active</span>';
                    }
                    return '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function($row) {
                    $editUrl = box_route('whatsapp.chatbots.edit', $row->id);
                    $deleteUrl = box_route('whatsapp.chatbots.destroy', $row->id);
                    $csrf = csrf_token();
                    return '
                        <a href="'.$editUrl.'" class="btn btn-sm btn-info" title="Edit Builder">
                            <i class="ti ti-edit"></i> Edit Flow
                        </a>
                        <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure you want to delete this chatbot?\');">
                            <input type="hidden" name="_token" value="'.$csrf.'">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['trigger_badge', 'status_badge', 'action'])
                ->make(true);
        }
        
        $projects = \App\Models\WhatsAppProject::where('box_id', $boxId)->get();
        
        return view('frontend.whatsapp.chatbots.index', compact('projects'));
    }

    public function demoBuilder($boxSlug)
    {
        return view('frontend.whatsapp.demo_builder');
    }

    public function create(Request $request, $boxSlug)
    {
        $boxId = $this->getBoxId($boxSlug);
        $chatbot = new WhatsAppChatbot();
        if ($request->filled('project_id')) {
            $chatbot->whatsapp_project_id = $request->project_id;
        }
        $projects = \App\Models\WhatsAppProject::where('box_id', $boxId)->get();
        return view('frontend.whatsapp.chatbot_builder', compact('chatbot', 'projects'));
    }

    public function store(Request $request, $boxSlug)
    {
        $boxId = $this->getBoxId($boxSlug);
        
        $validated = $request->validate([
            'whatsapp_project_id' => 'nullable|exists:whats_app_projects,id',
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string',
            'trigger_value' => 'nullable|string',
            'flow_data' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['box_id'] = $boxId;
        $chatbot = WhatsAppChatbot::create($validated);

        return response()->json(['success' => true, 'chatbot' => $chatbot]);
    }

    public function edit($boxSlug, $id)
    {
        $boxId = $this->getBoxId($boxSlug);
        $chatbot = WhatsAppChatbot::where('box_id', $boxId)->findOrFail($id);
        $projects = \App\Models\WhatsAppProject::where('box_id', $boxId)->get();
        return view('frontend.whatsapp.chatbot_builder', compact('chatbot', 'projects'));
    }

    public function update(Request $request, $boxSlug, $id)
    {
        $boxId = $this->getBoxId($boxSlug);
        $chatbot = WhatsAppChatbot::where('box_id', $boxId)->findOrFail($id);

        $validated = $request->validate([
            'whatsapp_project_id' => 'nullable|exists:whats_app_projects,id',
            'name' => 'required|string|max:255',
            'trigger_type' => 'required|string',
            'trigger_value' => 'nullable|string',
            'flow_data' => 'nullable|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $chatbot->update($validated);

        return response()->json(['success' => true, 'chatbot' => $chatbot]);
    }

    public function destroy($boxSlug, $id)
    {
        $boxId = $this->getBoxId($boxSlug);
        $chatbot = WhatsAppChatbot::where('box_id', $boxId)->findOrFail($id);
        $chatbot->delete();

        return redirect()->route('whatsapp.chatbots.index', ['box_slug' => $boxSlug])->with('success', 'Chatbot deleted successfully.');
    }
}
