<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->limit(10)->get()->each(function (User $user): void {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'database.seeded',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Welcome to CarryCaro',
                    'body' => 'Your sample notification is ready.',
                ]),
                'read_at' => fake()->boolean() ? now() : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
