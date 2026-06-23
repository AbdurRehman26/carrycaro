<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StartChatConversationRequest;
use App\Http\Requests\StoreChatMessageRequest;
use App\Http\Resources\ChatConversationResource;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatConversation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class ChatController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $conversations = ChatConversation::query()
            ->forUser($user)
            ->with(['userOne', 'userTwo', 'trip.fromCity.country', 'trip.toCity.country', 'trip.airlineRecord', 'lastMessage.sender'])
            ->withCount([
                'messages as unread_messages_count' => fn (Builder $query) => $query
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at'),
            ])
            ->orderByRaw('COALESCE(last_message_at, created_at) DESC')
            ->paginate($request->integer('per_page', 15));

        return ChatConversationResource::collection($conversations);
    }

    public function store(StartChatConversationRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $otherUserId = (int) $request->validated('user_id');
        $tripId = $request->validated('trip_id');
        [$userOneId, $userTwoId] = $this->normalizeParticipantIds($userId, $otherUserId);

        $query = ChatConversation::query()
            ->where('user_one_id', $userOneId)
            ->where('user_two_id', $userTwoId);

        $tripId === null
            ? $query->whereNull('trip_id')
            : $query->where('trip_id', $tripId);

        $conversation = $query->first();

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
                'trip_id' => $tripId,
            ]);
        }

        $conversation->load(['userOne', 'userTwo', 'trip.fromCity.country', 'trip.toCity.country', 'trip.airlineRecord', 'lastMessage.sender']);

        return response()->json([
            'conversation' => new ChatConversationResource($conversation),
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    public function show(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);

        $conversation->load(['userOne', 'userTwo', 'trip.fromCity.country', 'trip.toCity.country', 'trip.airlineRecord', 'lastMessage.sender']);
        $conversation->loadCount([
            'messages as unread_messages_count' => fn (Builder $query) => $query
                ->where('sender_id', '!=', $request->user()->id)
                ->whereNull('read_at'),
        ]);

        return response()->json([
            'conversation' => new ChatConversationResource($conversation),
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation): AnonymousResourceCollection
    {
        $this->authorizeParticipant($request, $conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->oldest()
            ->paginate($request->integer('per_page', 25));

        return ChatMessageResource::collection($messages);
    }

    public function send(StoreChatMessageRequest $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        return response()->json([
            'message' => 'Message sent successfully.',
            'chat_message' => new ChatMessageResource($message->load('sender')),
        ], 201);
    }

    public function markAsRead(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeParticipant($request, $conversation);

        $updated = $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Conversation marked as read.',
            'read_messages_count' => $updated,
        ]);
    }

    private function authorizeParticipant(Request $request, ChatConversation $conversation): void
    {
        abort_unless($conversation->includesUser($request->user()), 403, 'Unauthorized.');
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function normalizeParticipantIds(int $firstUserId, int $secondUserId): array
    {
        return [
            min($firstUserId, $secondUserId),
            max($firstUserId, $secondUserId),
        ];
    }
}
