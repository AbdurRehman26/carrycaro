<?php

namespace Database\Seeders;

use App\Models\ChatConversation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->limit(12)->get();
        $trips = Trip::query()->latest()->limit(10)->get();

        if ($users->count() < 2) {
            return;
        }

        for ($index = 0; $index < min(8, $users->count() - 1); $index++) {
            $firstUser = $users[$index];
            $secondUser = $users[$index + 1];
            $trip = $trips->get($index);
            [$userOneId, $userTwoId] = [
                min($firstUser->id, $secondUser->id),
                max($firstUser->id, $secondUser->id),
            ];

            $conversation = ChatConversation::query()->create([
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
                'trip_id' => $trip?->id,
            ]);

            $firstMessage = $conversation->messages()->create([
                'sender_id' => $firstUser->id,
                'body' => 'Hi, is this luggage space still available?',
                'created_at' => now()->subMinutes(20 - $index),
                'updated_at' => now()->subMinutes(20 - $index),
            ]);

            $secondMessage = $conversation->messages()->create([
                'sender_id' => $secondUser->id,
                'body' => 'Yes, I can carry it if the package is sealed.',
                'read_at' => $index % 2 === 0 ? now()->subMinutes(5) : null,
                'created_at' => now()->subMinutes(10 - $index),
                'updated_at' => now()->subMinutes(10 - $index),
            ]);

            $conversation->update([
                'last_message_at' => $secondMessage->created_at ?? $firstMessage->created_at,
            ]);
        }
    }
}
