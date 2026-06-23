<?php

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\City;
use App\Models\Country;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function createChatTripFixture(User $user): Trip
{
    $fromCountry = Country::create(['name' => 'Germany', 'code' => 'DE']);
    $toCountry = Country::create(['name' => 'United Arab Emirates', 'code' => 'AE']);
    $fromCity = City::create(['name' => 'Berlin', 'country_id' => $fromCountry->id, 'city_type' => 'admin']);
    $toCity = City::create(['name' => 'Dubai', 'country_id' => $toCountry->id, 'city_type' => 'admin']);

    return Trip::create([
        'user_id' => $user->id,
        'from_city_id' => $fromCity->id,
        'to_city_id' => $toCity->id,
        'departure_date' => now()->addDays(5),
        'arrival_date' => now()->addDays(6),
        'weight_available' => 5,
        'weight_price' => '25',
    ]);
}

it('requires authentication for chat endpoints', function (string $method, string $uri) {
    $this->json($method, $uri)
        ->assertUnauthorized();
})->with([
    ['GET', '/api/conversations'],
    ['POST', '/api/conversations'],
    ['GET', '/api/conversations/1'],
    ['GET', '/api/conversations/1/messages'],
    ['POST', '/api/conversations/1/messages'],
    ['PATCH', '/api/conversations/1/read'],
]);

it('starts a trip conversation and reuses the same pair and trip', function () {
    $traveler = User::factory()->create();
    $sender = User::factory()->create();
    $trip = createChatTripFixture($traveler);

    Sanctum::actingAs($sender);

    $firstResponse = $this->postJson('/api/conversations', [
        'user_id' => $traveler->id,
        'trip_id' => $trip->id,
    ]);

    $firstResponse
        ->assertCreated()
        ->assertJsonPath('conversation.other_user.id', $traveler->id)
        ->assertJsonPath('conversation.trip.id', $trip->id)
        ->assertJsonPath('conversation.unread_count', 0);

    $conversationId = $firstResponse->json('conversation.id');

    $this->postJson('/api/conversations', [
        'user_id' => $traveler->id,
        'trip_id' => $trip->id,
    ])
        ->assertOk()
        ->assertJsonPath('conversation.id', $conversationId);

    expect(ChatConversation::count())->toBe(1);
});

it('validates starting a conversation', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/conversations', [
        'user_id' => $user->id,
        'trip_id' => 999,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['user_id', 'trip_id']);
});

it('sends lists and marks messages as read', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $conversation = ChatConversation::create([
        'user_one_id' => min($sender->id, $recipient->id),
        'user_two_id' => max($sender->id, $recipient->id),
    ]);

    Sanctum::actingAs($sender);

    $sendResponse = $this->postJson('/api/conversations/'.$conversation->id.'/messages', [
        'body' => 'Can you carry a small package?',
    ]);

    $sendResponse
        ->assertCreated()
        ->assertJsonPath('message', 'Message sent successfully.')
        ->assertJsonPath('chat_message.body', 'Can you carry a small package?')
        ->assertJsonPath('chat_message.sender.id', $sender->id);

    $messageId = $sendResponse->json('chat_message.id');

    $this->getJson('/api/conversations/'.$conversation->id.'/messages')
        ->assertOk()
        ->assertJsonPath('data.0.id', $messageId)
        ->assertJsonPath('data.0.read_at', null);

    Sanctum::actingAs($recipient);

    $this->getJson('/api/conversations')
        ->assertOk()
        ->assertJsonPath('data.0.unread_count', 1)
        ->assertJsonPath('data.0.last_message.body', 'Can you carry a small package?');

    $this->patchJson('/api/conversations/'.$conversation->id.'/read')
        ->assertOk()
        ->assertJsonPath('message', 'Conversation marked as read.')
        ->assertJsonPath('read_messages_count', 1);

    expect(ChatMessage::find($messageId)->read_at)->not->toBeNull();
});

it('prevents non participants from viewing or messaging a conversation', function () {
    $owner = User::factory()->create();
    $recipient = User::factory()->create();
    $outsider = User::factory()->create();
    $conversation = ChatConversation::create([
        'user_one_id' => min($owner->id, $recipient->id),
        'user_two_id' => max($owner->id, $recipient->id),
    ]);

    Sanctum::actingAs($outsider);

    $this->getJson('/api/conversations/'.$conversation->id)->assertForbidden();
    $this->getJson('/api/conversations/'.$conversation->id.'/messages')->assertForbidden();
    $this->postJson('/api/conversations/'.$conversation->id.'/messages', [
        'body' => 'Hello',
    ])->assertForbidden();
    $this->patchJson('/api/conversations/'.$conversation->id.'/read')->assertForbidden();
});

it('validates chat messages', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $conversation = ChatConversation::create([
        'user_one_id' => min($sender->id, $recipient->id),
        'user_two_id' => max($sender->id, $recipient->id),
    ]);

    Sanctum::actingAs($sender);

    $this->postJson('/api/conversations/'.$conversation->id.'/messages', [
        'body' => '',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body']);
});
