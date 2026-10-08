<?php

namespace App\Services;

class AuditHistoryService
{
    /**
     * Get a formatted array of audit history for any Auditable model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model An instance of a model that uses OwenIt\Auditing\Auditable
     * @return \Illuminate\Support\Collection
     */
    public function getFormattedHistory($model)
    {
        // Ensure the model actually has audits
        if (!method_exists($model, 'audits')) {
            return collect([]);
        }

        $audits = $model->audits()->with('user')->orderBy('created_at', 'desc')->get();
        
        return $audits->map(function($audit) {
            $changes = [];
            
            if (is_array($audit->new_values)) {
                foreach ($audit->new_values as $key => $value) {
                    // Skip tracking common timestamps and user IDs to keep history clean
                    if (in_array($key, ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'])) {
                        continue;
                    }
                    
                    $old = $audit->old_values[$key] ?? 'N/A';
                    
                    // Basic formatting to make keys human readable
                    $formattedKey = ucfirst(str_replace('_', ' ', $key));
                    
                    $changes[] = "<strong>{$formattedKey}</strong>: " . ($old ?: 'N/A') . " &rarr; " . ($value ?: 'N/A');
                }
            }
            
            return [
                'action' => ucfirst($audit->event),
                'user' => $audit->user ? $audit->user->name : 'System',
                'date' => $audit->created_at->format('d M Y h:i A'),
                'changes' => count($changes) > 0 ? implode('<br>', $changes) : 'No specific field changes'
            ];
        });
    }
}
