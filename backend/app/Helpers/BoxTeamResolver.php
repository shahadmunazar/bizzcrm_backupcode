<?php

namespace App\Helpers;

use Spatie\Permission\Contracts\Team;
use Spatie\Permission\DefaultTeamResolver;

class BoxTeamResolver extends DefaultTeamResolver
{
    /**
     * Resolve the team ID from the current context (BoxUser's box_id)
     */
    public function findTeamById($teamId): ?Team
    {
        // For box-based permissions, we don't need a Team model
        // Just return the box_id as the team identifier
        return $teamId;
    }
}

