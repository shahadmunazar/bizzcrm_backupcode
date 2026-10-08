<?php

namespace App\Jobs;

use App\Models\Box;
use App\Models\BoxUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeEmployeeIDPrefix implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected int $boxId;
    protected $changeDynamicPrefix;

    public function __construct(int $boxId, $changeDynamicPrefix)
    {
        $this->boxId = $boxId;
        $this->changeDynamicPrefix = $changeDynamicPrefix;

        Log::info("ChangeEmployeeIDPrefix job initialized with Box ID: {$this->boxId}, ChangeDynamicPrefix: {$this->changeDynamicPrefix}");
    }

    public function handle(): void
    {
        try {
            Log::info("ChangeEmployeeIDPrefix job started for Box ID: {$this->boxId}");

            $box = Box::find($this->boxId);

            if (!$box) {
                Log::warning("Box not found for ID: {$this->boxId}");
                return;
            }

            Log::info("Box fetched", ['box' => $box->toArray()]);
            if ((string)$box->change_dynamic_prefix !== '1') {
                Log::info("Box change_dynamic_prefix is not enabled (value: '{$box->change_dynamic_prefix}'); skipping.");
                return;
            }
            if ((string)$box->dynamic_employee_id == '1' && !empty($box->custom_employee_id)) {
                $prefix = strtoupper(trim($box->custom_employee_id));
                Log::info("Dynamic prefix enabled. Using custom_employee_id as prefix: '{$prefix}'");
            } else {
                $prefix = strtoupper(substr(trim($box->name ?? ''), 0, 2));
                Log::info("Dynamic prefix disabled or missing. Using name-based prefix: '{$prefix}'");
            }
            $users = BoxUser::where('box_id', $box->id)->get();
            Log::info("Total users fetched: " . $users->count());
            foreach ($users as $user) {
                $employeeId = $user->employee_id;
                if (empty($employeeId)) {
                    Log::warning("User ID {$user->id} skipped: empty employee_id");
                    continue;
                }
                $dashPos = strpos($employeeId, '-');
                if ($dashPos === false) {
                    Log::warning("User ID {$user->id} skipped: invalid employee_id format '{$employeeId}'");
                    continue;
                }
                $oldPrefix = substr($employeeId, 0, $dashPos);
                $suffix = substr($employeeId, $dashPos + 1);
                if (strcasecmp($oldPrefix, $prefix) !== 0) {
                    $newEmployeeId = $prefix . '-' . $suffix;
                    $user->employee_id = $newEmployeeId;
                    $user->save();
                    Log::info("User ID {$user->id} employee_id updated from '{$employeeId}' to '{$newEmployeeId}'");
                } else {
                    Log::info("User ID {$user->id} already has correct prefix '{$prefix}'");
                }
            }
            Log::info("ChangeEmployeeIDPrefix job completed for Box ID: {$this->boxId}");
        } catch (\Throwable $e) {
            Log::error("Exception in ChangeEmployeeIDPrefix job: " . $e->getMessage(), ['exception' => $e]);
        }
    }
}
