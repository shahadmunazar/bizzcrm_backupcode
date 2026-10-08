<?php

namespace App\Services;

use App\Models\WhatsAppQuickReply;
use App\Models\Box;
use Illuminate\Support\Facades\Auth;

class WhatsAppQuickReplyService
{
    /**
     * Get the Box ID from a slug.
     */
    public function getBoxId(string $boxSlug): ?int
    {
        $box = Box::where('slug', $boxSlug)->first();
        return $box ? $box->id : null;
    }

    /**
     * Get the query builder for quick replies.
     */
    public function getQuery(int $boxId)
    {
        return WhatsAppQuickReply::where('box_id', $boxId);
    }

    /**
     * Store a new quick reply.
     */
    public function store(array $data, int $boxId)
    {
        $data['box_id'] = $boxId;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        return WhatsAppQuickReply::create($data);
    }

    /**
     * Update an existing quick reply.
     */
    public function update(array $data, int $boxId, int $id)
    {
        $reply = WhatsAppQuickReply::where('box_id', $boxId)->findOrFail($id);
        
        $data['updated_by'] = Auth::id();
        $reply->update($data);

        return $reply;
    }

    /**
     * Delete a quick reply.
     */
    public function delete(int $boxId, int $id)
    {
        $reply = WhatsAppQuickReply::where('box_id', $boxId)->findOrFail($id);
        $reply->deleted_by = Auth::id();
        $reply->save();
        $reply->delete();

        return true;
    }
}
