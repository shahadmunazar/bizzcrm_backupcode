<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request, $box_slug = null)
    {
        if ($request->ajax()) {
            $query = Faq::query()->orderBy('order', 'asc');
            return \Yajra\DataTables\Facades\DataTables::of($query)
                ->setRowAttr([
                    'data-id' => function($faq) {
                        return $faq->id;
                    }
                ])
                ->editColumn('question', function ($faq) {
                    return \Illuminate\Support\Str::limit($faq->question, 50);
                })
                ->addColumn('status', function ($faq) {
                    $checked = $faq->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch"><input class="form-check-input toggle-status" type="checkbox" data-id="'.$faq->id.'" '.$checked.'></div>';
                })
                ->addColumn('action', function ($faq) {
                    $html = '
                    <div class="dropdown table-action">
                        <a href="#" class="action-icon" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#edit_faq_'.$faq->id.'"><i class="ti ti-edit text-blue"></i> Edit</a>
                            <a class="dropdown-item delete-faq" href="javascript:void(0);" data-id="'.$faq->id.'"><i class="ti ti-trash text-danger"></i> Delete</a>
                        </div>
                    </div>';

                    // Edit Offcanvas HTML
                    $checked_active = $faq->is_active ? 'checked' : '';
                    $html .= '
                    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="edit_faq_'.$faq->id.'">
                        <div class="offcanvas-header border-bottom">
                            <h5 class="fw-semibold">Edit FAQ</h5>
                            <button type="button" class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
                        </div>
                        <div class="offcanvas-body">
                            <form class="edit-faq-form" action="'.box_route("superadmin.faqs.update", $faq->id).'" method="POST">
                                '.csrf_field().'
                                '.method_field("PUT").'
                                <div class="mb-3">
                                    <label class="col-form-label">Question <span class="text-danger">*</span></label>
                                    <input type="text" name="question" class="form-control" value="'.htmlspecialchars($faq->question).'" required>
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Answer <span class="text-danger">*</span></label>
                                    <textarea name="answer" class="form-control summernote" rows="5" required>'.htmlspecialchars($faq->answer).'</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="col-form-label">Order</label>
                                    <input type="number" name="order" class="form-control" value="'.$faq->order.'">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" '.$checked_active.'>
                                        <label class="form-check-label">Active Status</label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="offcanvas">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>';

                    return $html;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        return view('frontend.superadmin.faqs');
    }

    public function store(Request $request, $box_slug = null)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer'
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'order' => $request->order ?? 0
        ]);

        return response()->json(['success' => true, 'message' => 'FAQ added successfully!']);
    }

    public function update(Request $request, $box_slug, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'nullable|integer'
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'order' => $request->order ?? 0
        ]);

        return response()->json(['success' => true, 'message' => 'FAQ updated successfully!']);
    }

    public function destroy($box_slug, $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return response()->json(['success' => true, 'message' => 'FAQ deleted successfully!']);
    }

    public function toggleStatus($box_slug, $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->is_active = !$faq->is_active;
        $faq->save();

        return response()->json(['success' => true, 'message' => 'FAQ status updated successfully!']);
    }

    public function reorder($box_slug, Request $request)
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $order) {
            Faq::where('id', $order['id'])->update(['order' => $order['order']]);
        }

        return response()->json(['success' => true, 'message' => 'FAQs reordered successfully!']);
    }
}
