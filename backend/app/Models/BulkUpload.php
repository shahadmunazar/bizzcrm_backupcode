<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class BulkUpload extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'box_id',
        'user_id',
        'file_name',
        'original_file_name',
        'file_path',
        'file_type',
        'file_size',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'skipped_rows',
        'duplicate_rows',
        'percentage',
        'status',
        'error_message',
        'mappings',
        'create_duplicate',
        'started_at',
        'processed_at',
        'completed_at',
        'report_file_path',
        'report_file_url',
        'report_status',
        'report_progress',
        'report_requested_at',
        'report_generated_at',
        'report_requested_by',
        'report_error',
    ];

    protected $casts = [
        'mappings' => 'array',
        'create_duplicate' => 'boolean',
        'file_size' => 'integer',
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'successful_rows' => 'integer',
        'skipped_rows' => 'integer',
        'duplicate_rows' => 'integer',
        'percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'report_requested_at' => 'datetime',
        'report_generated_at' => 'datetime',
        'report_progress' => 'decimal:2',
    ];

    /**
     * Get the box that owns the bulk upload
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /**
     * Get the user who uploaded the file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all leads imported from this bulk upload
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
