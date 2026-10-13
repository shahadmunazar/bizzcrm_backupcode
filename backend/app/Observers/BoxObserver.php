<?php

namespace App\Observers;

use App\Models\Box;
use App\Jobs\CreateStandardShiftJob;
use App\Jobs\DepartmentCreateJob;

class BoxObserver
{
    /**
     * Handle the Box "created" event.
     */
    public function created(Box $box): void
    {
        // Automatically create standard shift for newly created box
        dispatch(new CreateStandardShiftJob($box->id));

        // Automatically create default departments and designations
        dispatch(new DepartmentCreateJob($box->id));
    }
}
