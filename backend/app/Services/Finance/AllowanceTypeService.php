<?php

namespace App\Services\Finance;

use App\Models\AllowanceType;
use App\Models\AllowancePolicy;
use App\Models\Box;
use Illuminate\Support\Facades\DB;

/**
 * Service class for managing Allowance Types.
 * Handles the creation, updating, deletion, and role-syncing policies for allowances.
 */
class AllowanceTypeService
{
    /**
     * Retrieve all allowance types for a specific box.
     *
     * @param Box $box The current active box.
     * @return \Illuminate\Database\Eloquent\Collection List of allowance types with relationships.
     */
    public function getAll(Box $box)
    {
        return AllowanceType::with(['creator', 'updater', 'policies'])
            ->where('box_id', $box->id)
            ->get();
    }

    /**
     * Create a new allowance type and assign it to specified roles.
     *
     * @param Box $box The current active box.
     * @param array $data The validated input data (name, amount, roles, etc.).
     * @param int $userId The ID of the user creating the allowance.
     * @return AllowanceType The newly created allowance type.
     */
    public function create(Box $box, array $data, $userId)
    {
        return DB::transaction(function () use ($box, $data, $userId) {
            $data['box_id'] = $box->id;
            $data['created_by'] = $userId;
            $data['is_active'] = true;

            $allowanceType = AllowanceType::create($data);

            // Create policies mapping this allowance to specific roles
            if (!empty($data['roles'])) {
                foreach ($data['roles'] as $role) {
                    AllowancePolicy::create([
                        'box_id' => $box->id,
                        'allowance_type_id' => $allowanceType->id,
                        'role_name' => $role,
                        'is_active' => true,
                        'created_by' => $userId
                    ]);
                }
            }

            return $allowanceType;
        });
    }

    /**
     * Update an existing allowance type and refresh its assigned roles.
     *
     * @param Box $box The current active box.
     * @param int $id The ID of the allowance type to update.
     * @param array $data The validated input data.
     * @param int $userId The ID of the user updating the allowance.
     * @return AllowanceType The updated allowance type.
     */
    public function update(Box $box, $id, array $data, $userId)
    {
        return DB::transaction(function () use ($box, $id, $data, $userId) {
            $allowanceType = AllowanceType::where('box_id', $box->id)->findOrFail($id);
            $data['updated_by'] = $userId;
            $allowanceType->update($data);

            // Remove old role assignments before adding new ones
            $allowanceType->policies()->whereNotNull('role_name')->delete();
            
            // Recreate policies with updated roles
            if (!empty($data['roles'])) {
                foreach ($data['roles'] as $role) {
                    AllowancePolicy::create([
                        'box_id' => $box->id,
                        'allowance_type_id' => $allowanceType->id,
                        'role_name' => $role,
                        'is_active' => true,
                        'created_by' => $userId, 
                        'updated_by' => $userId
                    ]);
                }
            }

            return $allowanceType;
        });
    }

    /**
     * Permanently delete an allowance type.
     *
     * @param Box $box The current active box.
     * @param int $id The ID of the allowance type.
     * @return bool True if deleted successfully.
     */
    public function delete(Box $box, $id)
    {
        $allowanceType = AllowanceType::where('box_id', $box->id)->findOrFail($id);
        return $allowanceType->delete();
    }

    /**
     * Toggle the active/inactive status of an allowance type.
     *
     * @param Box $box The current active box.
     * @param int $id The ID of the allowance type.
     * @return bool True if status updated successfully.
     */
    public function toggleStatus(Box $box, $id)
    {
        $allowanceType = AllowanceType::where('box_id', $box->id)->findOrFail($id);
        $allowanceType->is_active = !$allowanceType->is_active;
        return $allowanceType->save();
    }
}
