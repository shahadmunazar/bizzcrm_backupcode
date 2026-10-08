<?php

namespace App\Http\Controllers;

use App\Models\ProductCompany;
use App\Models\ProductSalt;
use App\Models\ProductHsn;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    protected $box;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->box = $request->attributes->get('box');
            return $next($request);
        });
    }

    public function storeCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|max:50'
        ]);

        $company = ProductCompany::create([
            'name' => $request->name,
            'status' => $request->status ?? 'Yes',
            'box_id' => $this->box->id
        ]);

        return response()->json([
            'success' => true,
            'id' => $company->name,
            'name' => $company->name,
            'status' => $company->status
        ]);
    }

    public function storeSalt(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|string|max:50'
        ]);

        $salt = ProductSalt::create([
            'name' => $request->name,
            'status' => $request->status ?? 'Yes',
            'box_id' => $this->box->id
        ]);

        return response()->json([
            'success' => true,
            'id' => $salt->name,
            'name' => $salt->name,
            'status' => $salt->status
        ]);
    }

    public function storeHsn(Request $request, \App\Services\ProductHsnService $productHsnService)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'sgst' => 'required|numeric',
            'cgst' => 'required|numeric',
            'igst' => 'required|numeric',
            'type' => 'required|string',
            'uqc' => 'nullable|string|max:255',
            'cess' => 'required|numeric',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'status' => 'nullable|string'
        ]);
        
        $validatedData['status'] = $validatedData['status'] ?? 'Yes';

        $hsn = $productHsnService->store($validatedData, $this->box->id, auth()->id());

        return response()->json([
            'success' => true,
            'id' => $hsn->code,
            'code' => $hsn->code,
            'status' => $hsn->status
        ]);
    }

    public function list(Request $request)
    {
        $type = $request->query('type');
        $box_id = $this->box->id;
        $data = [];

        if ($type === 'company') {
            // Match the model used in ProductController@create
            $items = \App\Models\Company::where('box_id', $box_id)->get();
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->bill_name, // The value in the dropdown is bill_name
                    'name' => $item->bill_name
                ];
            }
        } elseif ($type === 'salt') {
            $items = ProductSalt::where('box_id', $box_id)->get();
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->name,
                    'name' => $item->name
                ];
            }
        } elseif ($type === 'hsn') {
            $items = ProductHsn::where('box_id', $box_id)->get();
            foreach ($items as $item) {
                $data[] = [
                    'id' => $item->code,
                    'name' => $item->code
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
