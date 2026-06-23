<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'other_user' => $user ? new UserResource($this->otherUserFor($user)) : null,
            'trip' => new TripResource($this->whenLoaded('trip')),
            'last_message' => new ChatMessageResource($this->whenLoaded('lastMessage')),
            'unread_count' => (int) ($this->unread_messages_count ?? 0),
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
