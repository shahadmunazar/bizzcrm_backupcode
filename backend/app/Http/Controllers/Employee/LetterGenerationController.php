<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LetterSignatory;
use App\Models\LetterHead;
use App\Models\LetterTemplate;
use App\Models\EmployeeLetter;
use App\Models\LetterType;
use App\Models\BoxUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LetterGenerationController extends Controller
{
    private function getBoxId()
    {
        $boxUser = Auth::user()->box_user ?? BoxUser::where('user_id', Auth::id())->first();
        return $boxUser->box_id ?? null;
    }

    public function index()
    {
        $boxId = $this->getBoxId();
        
        $signatories = LetterSignatory::with('user')->where('box_id', $boxId)->get();
        $letterHeads = LetterHead::where('box_id', $boxId)->get();
        $templates = LetterTemplate::with('signatory.user')->where('box_id', $boxId)->get();
        $pendingLetters = EmployeeLetter::with('user', 'template')->where('box_id', $boxId)->where('status', 'Pending')->get();
        $historyLetters = EmployeeLetter::with('user', 'template')->where('box_id', $boxId)->where('status', 'Released')->get();
        $letterTypes = LetterType::where('box_id', $boxId)->get();
        
        $employees = User::whereHas('box_user_new', function($q) use ($boxId) {
            $q->where('box_id', $boxId);
        })->get();

        return view('frontend.attendance.employee.letter_generation', compact(
            'signatories', 'letterHeads', 'templates', 'pendingLetters', 'historyLetters', 'employees', 'letterTypes'
        ));
    }

    public function getLetterTypesAPI(Request $request)
    {
        $boxId = $this->getBoxId();
        $letterTypes = LetterType::where('box_id', $boxId)->get();
        return response()->json(['status' => true, 'data' => $letterTypes]);
    }

    public function getLetterHeadsAPI(Request $request)
    {
        $boxId = $this->getBoxId();
        $letterHeads = LetterHead::where('box_id', $boxId)->get();
        return response()->json(['status' => true, 'data' => $letterHeads]);
    }

    public function getSignatoriesAPI(Request $request)
    {
        $boxId = $this->getBoxId();
        $signatories = LetterSignatory::with('user')->where('box_id', $boxId)->get();
        return response()->json(['status' => true, 'data' => $signatories]);
    }

    public function getTabHTML(Request $request, $tabName)
    {
        $boxId = $this->getBoxId();
        $html = '';

        if ($tabName === 'signatories') {
            $signatories = LetterSignatory::with('user')->where('box_id', $boxId)->get();
            $employees = User::whereHas('box_user_new', function($q) use ($boxId) {
                $q->where('box_id', $boxId);
            })->get();
            $html = view('frontend.attendance.employee.partials.signatories_tab', compact('signatories', 'employees'))->render();
        } 
        elseif ($tabName === 'letter_heads') {
            $letterHeads = LetterHead::where('box_id', $boxId)->get();
            $html = view('frontend.attendance.employee.partials.letter_heads_tab', compact('letterHeads'))->render();
        }
        elseif ($tabName === 'templates') {
            $templates = LetterTemplate::with('signatory.user')->where('box_id', $boxId)->get();
            $letterTypes = LetterType::where('box_id', $boxId)->get();
            $signatories = LetterSignatory::with('user')->where('box_id', $boxId)->get();
            $html = view('frontend.attendance.employee.partials.templates_tab', compact('templates', 'letterTypes', 'signatories'))->render();
        }
        elseif ($tabName === 'letter_types') {
            $letterTypes = LetterType::where('box_id', $boxId)->get();
            $data = $letterTypes;
            $html = ''; // We will render via JS
        }
        elseif ($tabName === 'pending_letters') {
            $pendingLetters = EmployeeLetter::with('user', 'template')->where('box_id', $boxId)->where('status', 'Pending')->get();
            $html = view('frontend.attendance.employee.partials.pending_letters_tab', compact('pendingLetters'))->render();
        }
        elseif ($tabName === 'history_letters') {
            $historyLetters = EmployeeLetter::with('user', 'template')->where('box_id', $boxId)->where('status', 'Released')->get();
            $html = view('frontend.attendance.employee.partials.history_letters_tab', compact('historyLetters'))->render();
        }

        $response = ['status' => true, 'html' => $html];
        if (isset($data)) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }

    public function storeSignatory(Request $request, $boxSlug = null)
    {
        $request->validate([
            'user_id' => 'required',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('signatures', 'public');
        }

        LetterSignatory::create([
            'box_id' => $this->getBoxId(),
            'user_id' => $request->user_id,
            'signature_path' => $path,
            'status' => $request->status ? trim($request->status) : 'Active'
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Signatory added.']);
        }
        return redirect()->back()->with('success', 'Signatory added.');
    }

    public function updateSignatory(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        $request->validate([
            'user_id' => 'required',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'status' => 'required'
        ]);

        $data = [
            'user_id' => $request->user_id,
            'status' => $request->status ? trim($request->status) : 'Active'
        ];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('signatures', 'public');
            $data['signature_path'] = $path;
        }

        $signatory = LetterSignatory::where('id', $id)->where('box_id', $this->getBoxId())->first();
        if ($signatory) {
            $signatory->update($data);
        }

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Signatory updated.']);
        }
        return redirect()->back()->with('success', 'Signatory updated.');
    }

    public function deleteSignatory(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        LetterSignatory::where('id', $id)->where('box_id', $this->getBoxId())->delete();
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Signatory deleted.']);
        }
        return redirect()->back()->with('success', 'Signatory deleted.');
    }

    public function storeLetterHead(Request $request, $boxSlug = null)
    {
        $request->validate(['file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('letter_heads', 'public');
            LetterHead::create([
                'box_id' => $this->getBoxId(),
                'file_path' => $path,
                'status' => $request->status ? trim($request->status) : 'Active'
            ]);
        }
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter Head added.']);
        }
        return redirect()->back()->with('success', 'Letter Head added.');
    }

    public function updateLetterHead(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        $request->validate([
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'status' => 'required'
        ]);

        $data = ['status' => $request->status ? trim($request->status) : 'Active'];
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('letterheads', 'public');
            $data['file_path'] = $path;
        }

        $letterHead = LetterHead::where('id', $id)->where('box_id', $this->getBoxId())->first();
        if ($letterHead) {
            $letterHead->update($data);
        }

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter Head updated.']);
        }
        return redirect()->back()->with('success', 'Letter Head updated.');
    }

    public function deleteLetterHead(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        LetterHead::where('id', $id)->where('box_id', $this->getBoxId())->delete();
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter Head deleted.']);
        }
        return redirect()->back()->with('success', 'Letter Head deleted.');
    }

    public function storeTemplate(Request $request, $boxSlug = null)
    {
        $request->validate([
            'letter_type' => 'required',
            'template_name' => 'required',
            'signatory_id' => 'required'
        ]);

        LetterTemplate::create([
            'box_id' => $this->getBoxId(),
            'letter_type' => $request->letter_type,
            'template_name' => $request->template_name,
            'signatory_id' => $request->signatory_id,
            'content' => $request->content,
            'status' => $request->status ? trim($request->status) : 'Active'
        ]);
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Template saved.']);
        }
        return redirect()->back()->with('success', 'Template saved.');
    }

    public function updateTemplate(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        $request->validate([
            'template_name' => 'required',
            'letter_type' => 'required',
            'signatory_id' => 'required',
            'content' => 'required',
            'status' => 'required'
        ]);

        $template = LetterTemplate::where('id', $id)->where('box_id', $this->getBoxId())->first();
        if ($template) {
            $template->update([
                'template_name' => $request->template_name,
                'letter_type' => $request->letter_type,
                'signatory_id' => $request->signatory_id,
                'content' => $request->content,
                'status' => $request->status ? trim($request->status) : 'Active'
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Template updated.']);
        }
        return redirect()->back()->with('success', 'Template updated.');
    }

    public function getTemplates(Request $request)
    {
        $templates = LetterTemplate::where('box_id', $this->getBoxId())
            ->where('letter_type', $request->letter_type)
            ->get();
        return response()->json($templates);
    }

    public function getTemplateContent($boxSlug, $id)
    {
        $template = LetterTemplate::where('id', $id)->where('box_id', $this->getBoxId())->first();
        return response()->json(['content' => $template->content ?? '']);
    }
    public function previewLetter(Request $request, $boxSlug = null)
    {
        $request->validate([
            'user_id' => 'required',
            'content' => 'required'
        ]);

        $user = \App\Models\User::find($request->user_id);
        $employee = \App\Models\BoxUser::where('user_id', $request->user_id)->where('box_id', $this->getBoxId())->first();
        $content = $request->content;

        $variableMap = $request->input('variable_map', []);

        if ($user && $employee) {
            $systemData = [
                'employee_name' => $user->name ?? '',
                'first_name' => $employee->first_name ?? '',
                'last_name' => $employee->last_name ?? '',
                'employee_id' => $employee->employee_id ?? '',
                'address' => $employee->address ?? '',
                'date' => date('d-M-Y'),
                'designation' => $employee->designation ?? '',
                'department' => $employee->department ?? '',
                'employee_type' => $employee->emp_type ?? '',
                'mobile_no' => $user->mobile ?? $employee->phone ?? '',
                'email_id' => $user->email ?? '',
                'joining_date' => $employee->joining_date ? $employee->joining_date->format('d-M-Y') : '',
                'pan_number' => $employee->pan_number ?? '',
                'gender' => $employee->gender ?? '',
                'marital_status' => $employee->marital_status ?? '',
            ];

            if (is_array($variableMap) && count($variableMap) > 0) {
                foreach ($variableMap as $templateVar => $systemKey) {
                    if (array_key_exists($systemKey, $systemData)) {
                        $content = str_ireplace('{{' . $templateVar . '}}', $systemData[$systemKey], $content);
                        $content = str_ireplace('@{{' . $templateVar . '}}', $systemData[$systemKey], $content);
                    }
                }
            } else {
                foreach ($systemData as $tag => $value) {
                    $content = str_ireplace('{{' . $tag . '}}', $value, $content);
                    $content = str_ireplace('@{{' . $tag . '}}', $value, $content);
                }
            }
        }

        return response()->json(['success' => true, 'preview_html' => $content]);
    }

    public function generateLetter(Request $request, $boxSlug = null)
    {
        $request->validate([
            'user_id' => 'required',
            'template_id' => 'required'
        ]);

        $template = LetterTemplate::find($request->template_id);
        $user = \App\Models\User::find($request->user_id);
        $employee = \App\Models\BoxUser::where('user_id', $request->user_id)->where('box_id', $this->getBoxId())->first();
        $content = $request->content;
        $variableMap = $request->input('variable_map', []);

        if ($user && $employee) {
            $systemData = [
                'employee_name' => $user->name ?? '',
                'first_name' => $employee->first_name ?? '',
                'last_name' => $employee->last_name ?? '',
                'employee_id' => $employee->employee_id ?? '',
                'address' => $employee->address ?? '',
                'date' => date('d-M-Y'),
                'designation' => $employee->designation ?? '',
                'department' => $employee->department ?? '',
                'employee_type' => $employee->emp_type ?? '',
                'mobile_no' => $user->mobile ?? $employee->phone ?? '',
                'email_id' => $user->email ?? '',
                'joining_date' => $employee->joining_date ? $employee->joining_date->format('d-M-Y') : '',
                'pan_number' => $employee->pan_number ?? '',
                'gender' => $employee->gender ?? '',
                'marital_status' => $employee->marital_status ?? '',
            ];

            if (is_array($variableMap) && count($variableMap) > 0) {
                foreach ($variableMap as $templateVar => $systemKey) {
                    if (array_key_exists($systemKey, $systemData)) {
                        $content = str_ireplace('{{' . $templateVar . '}}', $systemData[$systemKey], $content);
                        $content = str_ireplace('@{{' . $templateVar . '}}', $systemData[$systemKey], $content);
                    }
                }
            } else {
                foreach ($systemData as $tag => $value) {
                    $content = str_ireplace('{{' . $tag . '}}', $value, $content);
                    $content = str_ireplace('@{{' . $tag . '}}', $value, $content);
                }
            }
        }

        EmployeeLetter::create([
            'box_id' => $this->getBoxId(),
            'user_id' => $request->user_id,
            'template_id' => $request->template_id,
            'content' => $content,
            'letter_type' => $template ? $template->letter_type : null,
            'status' => 'Released',
            'applied_date' => now(),
            'release_date' => now(),
            'requested_by' => Auth::user()->name
        ]);
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter generated successfully.']);
        }
        return redirect()->back()->with('success', 'Letter generated successfully.');
    }

    public function deleteLetter(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        EmployeeLetter::where('id', $id)->where('box_id', $this->getBoxId())->delete();
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter deleted.']);
        }
        return redirect()->back()->with('success', 'Letter deleted.');
    }

    public function storeLetterType(Request $request, $boxSlug = null)
    {
        $request->validate(['name' => 'required']);
        LetterType::create([
            'box_id' => $this->getBoxId(),
            'name' => $request->name,
            'status' => $request->status ? trim($request->status) : 'Active'
        ]);
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter Type added.']);
        }
        return redirect()->back()->with('success', 'Letter Type added.');
    }

    public function updateLetterType(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        $request->validate(['name' => 'required']);
        \Log::info("updateLetterType called with id: " . $id);
        \Log::info($request->all());
        $letterType = LetterType::where('id', $id)->where('box_id', $this->getBoxId())->first();
        if ($letterType) {
            $letterType->update([
                'name' => $request->name,
                'status' => trim($request->status)
            ]);
        }
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter Type updated.']);
        }
        return redirect()->back()->with('success', 'Letter Type updated.');
    }

    public function deleteLetterType(Request $request, $boxSlug, $id = null)
    {
        if (is_null($id)) {
            $id = $boxSlug;
            $boxSlug = request()->route('box_slug');
        }
        \Log::info($request->all());
        LetterType::where('id', $id)->where('box_id', $this->getBoxId())->delete();
        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Letter Type deleted.']);
        }
        return redirect()->back()->with('success', 'Letter Type deleted.');
    }
}
