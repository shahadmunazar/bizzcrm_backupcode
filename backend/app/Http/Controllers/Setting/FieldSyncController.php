<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Helpers\LeadFieldsHelper;
use App\Models\SystemField;
use App\Models\Box;
use App\Models\BoxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FieldSyncController extends Controller
{
    public function sync()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Find Box ID for the user
            $boxUserEntry = BoxUser::where('user_id', $user->id)->first();
            if (!$boxUserEntry) {
                 return response()->json(['error' => 'User is not assigned to a company (BoxUser not found).'], 404);
            }

            $isGlobalAdmin = $user->hasRole(['admin', 'superadmin'], 'web');
            $isBoxAdmin = $boxUserEntry && strtolower($boxUserEntry->staff_role ?? '') === 'admin';

            if (!($isGlobalAdmin || $isBoxAdmin || \App\Helpers\PermissionHelper::hasModulePermission('leads', 'update', 'manage_form'))) {
                 return response()->json(['error' => 'You do not have permission to sync fields.'], 403);
            }

            $boxId = $boxUserEntry->box_id;

            Log::info("Synchronous field sync started for Box ID: {$boxId}");

            $standardFields = LeadFieldsHelper::getFields();
            $existingFields = SystemField::where('box_id', $boxId)
                ->where('object', 'leads')
                ->get()
                ->keyBy('field');
            
            $processedFields = [];
            $createdFields = [];
            $updatedFields = [];
            $deletedFields = [];

            foreach ($standardFields as $fieldConfig) {
                $fieldConfig['box_id'] = $boxId;
                $fieldName = $fieldConfig['field'];
                if ($existingFields->has($fieldName)) {
                    $existingField = $existingFields->get($fieldName);
                    $existingField->update($fieldConfig);
                    $processedFields[] = $fieldName;
                    $updatedFields[] = $fieldName;
                } else {
                    SystemField::create($fieldConfig);
                    $processedFields[] = $fieldName;
                    $createdFields[] = $fieldName;
                }
            }

            // Identify extra fields to delete
            $extraFields = $existingFields->except($processedFields);
            if ($extraFields->isNotEmpty()) {
                $deletedFields = $extraFields->keys()->toArray();
                Log::info("Deleting extra fields for Box ID {$boxId}: " . implode(', ', $deletedFields));
                SystemField::whereIn('id', $extraFields->pluck('id'))->delete();
            }

            Log::info("Synchronous field sync completed for Box ID: {$boxId}");

            return response()->json([
                'success' => true,
                'message' => 'Lead fields synchronization completed successfully.',
                'box_id' => $boxId,
                'created' => $createdFields,
                'updated' => $updatedFields,
                'deleted' => $deletedFields
            ]);

        } catch (\Exception $e) {
            Log::error('Field sync failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to synchronize fields.'], 500);
        }
    }
}
