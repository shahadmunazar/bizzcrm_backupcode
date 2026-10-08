<?php

namespace App\Http\Controllers;

use App\Models\BulkImport;
use Illuminate\Http\Request;

class BulkImportController extends Controller
{
    public function status(Request $request, $box_slug, $type)
    {
        $box_id = request()->attributes->get('box')->id ?? null;
        $user_id = auth()->id();

        // Get the latest import of this type for this box and user
        $import = BulkImport::where('box_id', $box_id)
            ->where('user_id', $user_id)
            ->where('entity_type', $type)
            ->orderBy('id', 'desc')
            ->first();

        if (!$import) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $import->id,
                'status' => $import->status,
                'total_rows' => $import->total_rows,
                'processed_rows' => $import->processed_rows,
                'success_rows' => $import->success_rows,
                'skipped_rows' => $import->skipped_rows,
                'failed_rows' => count($import->errors ?? []),
                'errors' => $import->errors,
                'created_at' => $import->created_at->diffForHumans()
            ]
        ]);
    }
}
