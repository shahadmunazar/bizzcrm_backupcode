<?php
namespace App\Console\Commands;

use App\Models\Box;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentCreate extends Command
{
protected $signature = 'app:department-create-default {box_id?}';
    protected $description = 'Create default departments with a base team hierarchy using clean names';

    public function handle()
{
    $boxIdInput = $this->argument('box_id');
    $companies = $boxIdInput
        ? Box::where('id', $boxIdInput)->get()
        : Box::where('status', 'Enabled')->get();

    if ($companies->isEmpty()) {
        $this->error("No valid company found.");
        return;
    }

    $departments = ['Marketing', 'Production', 'Sales', 'HR', 'Finance'];

    foreach ($companies as $company) {
        DB::beginTransaction();

        try {
            $boxId  = $company->id;
            $userId = $company->superadmin_id;

            foreach ($departments as $departmentName) {
                $department = Department::firstOrCreate([
                    'box_id' => $boxId,
                    'name'   => $departmentName,
                ], [
                    'user_id'    => $userId,
                    'created_by' => $userId,
                    'status'     => 1,
                ]);

                $head = $this->createTeam($boxId, $department->id, "{$departmentName} 5", $userId);

                if ($departmentName === 'Marketing') {
                    $m51 = $this->createTeam($boxId, $department->id, 'MKT 5.1', $userId, $head);
                    $m511 = $this->createTeam($boxId, $department->id, 'MKT 5.1.1', $userId, $m51);
                    $this->createMultipleTeams($boxId, $department->id, [
                        'MKT 5.1.1.1', 'MKT 5.1.1.2', 'MKT 5.1.1.3', 'MKT 5.1.1.4',
                    ], $userId, $m511);

                    $m512 = $this->createTeam($boxId, $department->id, 'MKT 5.1.2', $userId, $m51);
                    $this->createMultipleTeams($boxId, $department->id, [
                        'MKT 5.1.2.1', 'MKT 5.1.2.2',
                    ], $userId, $m512);

                    $m52 = $this->createTeam($boxId, $department->id, 'MKT 5.2', $userId, $head);
                    $m521 = $this->createTeam($boxId, $department->id, 'MKT 5.2.1', $userId, $m52);
                    $this->createMultipleTeams($boxId, $department->id, [
                        'MKT 5.2.1.1', 'MKT 5.2.1.2', 'MKT 5.2.1.3', 'MKT 5.2.1.4',
                    ], $userId, $m521);

                    $m522 = $this->createTeam($boxId, $department->id, 'MKT 5.2.2', $userId, $m52);
                    $this->createMultipleTeams($boxId, $department->id, [
                        'MKT 5.2.2.1', 'MKT 5.2.2.2', 'MKT 5.2.2.3', 'MKT 5.2.2.4',
                    ], $userId, $m522);
                }

                Log::info("Created {$departmentName} department" . ($departmentName === 'Marketing' ? " with hierarchy" : "") . " for Box ID {$boxId}");
            }

            DB::commit();
            $this->info("Departments and hierarchy created for Box ID {$boxId}");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Failed for Box ID {$boxId}: " . $e->getMessage());
            $this->error("Error for Box ID {$boxId}: " . $e->getMessage());
        }
    }
}


    private function createTeam($boxId, $departmentId, $name, $userId, $parent = null)
    {
        $query = Team::where('box_id', $boxId)
            ->where('department_id', $departmentId)
            ->where('name', $name);

        if ($parent) {
            $query->where('parent_id', $parent->id);
        } else {
            $query->whereNull('parent_id');
        }
        $existing = $query->first();
        if ($existing) {
            return $existing;
        }
        $team = Team::create([
            'box_id'        => $boxId,
            'department_id' => $departmentId,
            'name'          => $name,
            'user_id'       => $userId,
            'created_by'    => $userId,
            'status'        => 1,
        ]);
        if ($parent) {
            $team->appendToNode($parent)->save();
        } else {
            $team->saveAsRoot();
        }
        return $team;
    }
    private function createMultipleTeams($boxId, $departmentId, array $names, $userId, $parent)
    {
        foreach ($names as $name) {
            $this->createTeam($boxId, $departmentId, $name, $userId, $parent);
        }
    }
}
