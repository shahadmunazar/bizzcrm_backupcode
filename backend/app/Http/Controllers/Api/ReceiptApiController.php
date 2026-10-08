<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Accounts\AccountController;

class ReceiptApiController extends Controller
{
    protected $webController;

    public function __construct(AccountController $webController)
    {
        $this->webController = $webController;
    }

    public function index(Request $request, $box_slug, $lead_id)
    {
        $request->merge(['lead_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getLeadReceipts($request);
    }

    public function store(Request $request, $box_slug, $lead_id)
    {
        $request->merge(['lead_id' => $lead_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->store($request);
    }
    
    public function update(Request $request, $box_slug, $lead_id, $receipt_id)
    {
        $request->merge(['lead_id' => $lead_id, 'id' => $receipt_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->update($request);
    }

    public function destroy(Request $request, $box_slug, $lead_id, $receipt_id)
    {
        $request->merge(['id' => $receipt_id]);
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->destroy($request);
    }

    public function getReceiptFormData(Request $request, $box_slug)
    {
        $request->route()->setParameter('box_slug', $box_slug);
        return $this->webController->getReceiptFormData($request);
    }
}
