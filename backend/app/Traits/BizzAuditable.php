<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

trait BizzAuditable
{
    /**
     * Boot the trait to handle model events.
     */
    public static function bootBizzAuditable()
    {
        static::creating(function ($model) {
            $userId = Auth::id();

            // Multi-tenant fallback: If no auth user, use the Box superadmin
            if (!$userId && isset($model->box_id)) {
                $box = \App\Models\Box::find($model->box_id);
                if ($box) {
                    $userId = $box->superadmin_id;
                }
            }

            static $schemaCache = [];
            $table = $model->getTable();
            
            if (!isset($schemaCache[$table]['added_by'])) {
                $schemaCache[$table]['added_by'] = \Illuminate\Support\Facades\Schema::hasColumn($table, 'added_by');
            }
            if (!isset($schemaCache[$table]['updated_by'])) {
                $schemaCache[$table]['updated_by'] = \Illuminate\Support\Facades\Schema::hasColumn($table, 'updated_by');
            }
            if (!isset($schemaCache[$table]['last_updated_by'])) {
                $schemaCache[$table]['last_updated_by'] = \Illuminate\Support\Facades\Schema::hasColumn($table, 'last_updated_by');
            }

            if ($schemaCache[$table]['added_by'] && !$model->added_by) {
                $model->added_by = $userId ?? 1;
            }
            if ($schemaCache[$table]['updated_by'] && !$model->updated_by) {
                $model->updated_by = $userId ?? 1;
            }
            if ($schemaCache[$table]['last_updated_by'] && !$model->last_updated_by) {
                $model->last_updated_by = $userId ?? 1;
            }
        });

        static::updating(function ($model) {
            $userId = Auth::id();

            if (!$userId && isset($model->box_id)) {
                $box = \App\Models\Box::find($model->box_id);
                if ($box) {
                    $userId = $box->superadmin_id;
                }
            }

            static $schemaCache = [];
            $table = $model->getTable();
            
            if (!isset($schemaCache[$table]['updated_by'])) {
                $schemaCache[$table]['updated_by'] = \Illuminate\Support\Facades\Schema::hasColumn($table, 'updated_by');
            }
            if (!isset($schemaCache[$table]['last_updated_by'])) {
                $schemaCache[$table]['last_updated_by'] = \Illuminate\Support\Facades\Schema::hasColumn($table, 'last_updated_by');
            }

            if ($schemaCache[$table]['updated_by'] && !$model->isDirty('updated_by')) {
                $model->updated_by = $userId ?? 1;
            }
            if ($schemaCache[$table]['last_updated_by'] && !$model->isDirty('last_updated_by')) {
                $model->last_updated_by = $userId ?? 1;
            }
        });

        static::deleting(function ($model) {
            // Check if SoftDeletes is being used and it's not a force delete
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                $userId = Auth::id();
                
                if (!$userId && isset($model->box_id)) {
                    $box = \App\Models\Box::find($model->box_id);
                    if ($box) {
                        $userId = $box->superadmin_id;
                    }
                }

                static $schemaCache = [];
                $table = $model->getTable();
                
                if (!isset($schemaCache[$table]['deleted_by'])) {
                    $schemaCache[$table]['deleted_by'] = \Illuminate\Support\Facades\Schema::hasColumn($table, 'deleted_by');
                }

                if ($schemaCache[$table]['deleted_by']) {
                    $model->deleted_by = $userId ?? 1;
                    $model->save();
                }
            }
        });
    }

    /**
     * Relationship to the user who added the record.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by')->withDefault(['name' => 'System']);
    }

    /**
     * Relationship to the user who last updated the record.
     */
    public function updatedBy(): BelongsTo
    {
        $column = array_key_exists('last_updated_by', $this->getAttributes()) ? 'last_updated_by' : 'updated_by';
        return $this->belongsTo(User::class, $column)->withDefault(['name' => 'System']);
    }

    /**
     * Relationship to the user who deleted the record (if soft deleted).
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withDefault(['name' => 'System']);
    }
}
