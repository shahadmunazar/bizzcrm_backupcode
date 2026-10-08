<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppQuickReplyService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WhatsAppQuickReplyController extends Controller
{
    protected $quickReplyService;

    public function __construct(WhatsAppQuickReplyService $quickReplyService)
    {
        $this->quickReplyService = $quickReplyService;
    }

    public function index(Request $request, $boxSlug)
    {
        $boxId = $this->quickReplyService->getBoxId($boxSlug);

        if ($request->ajax()) {
            $query = $this->quickReplyService->getQuery($boxId);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    return $row->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center justify-content-end">';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-info btn-edit-reply me-2" data-id="'.$row->id.'"><i class="ti ti-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-soft-danger btn-delete-reply" data-id="'.$row->id.'"><i class="ti ti-trash"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('whatsapp.quick_replies', compact('boxSlug'));
    }

    public function search(Request $request, $boxSlug)
    {
        $boxId = $this->quickReplyService->getBoxId($boxSlug);
        $query = $request->get('q', '');

        $replies = $this->quickReplyService->getQuery($boxId)
            ->where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('message', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'title', 'message']);

        return response()->json(['success' => true, 'replies' => $replies]);
    }

    public function store(Request $request, $boxSlug)
    {
        $boxId = $this->quickReplyService->getBoxId($boxSlug);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $reply = $this->quickReplyService->store($validated, $boxId);

        return response()->json(['success' => true, 'reply' => $reply, 'message' => 'Quick reply created successfully.']);
    }

    public function update(Request $request, $boxSlug, $id)
    {
        $boxId = $this->quickReplyService->getBoxId($boxSlug);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|boolean',
        ]);

        $reply = $this->quickReplyService->update($validated, $boxId, $id);

        return response()->json(['success' => true, 'reply' => $reply, 'message' => 'Quick reply updated successfully.']);
    }

    public function destroy($boxSlug, $id)
    {
        $boxId = $this->quickReplyService->getBoxId($boxSlug);
        
        $this->quickReplyService->delete($boxId, $id);

        return response()->json(['success' => true, 'message' => 'Quick reply deleted successfully.']);
    }
}
