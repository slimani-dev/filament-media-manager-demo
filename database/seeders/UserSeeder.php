<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Slimani\MediaManager\Models\File;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Seed Profile Avatar only if not set
        if (! $user->avatar_id) {
            $avatarFile = File::create([
                'name' => 'Profile Avatar',
                'size' => 0,
                'extension' => 'png',
                'mime_type' => 'image/png',
                'uploaded_by_user_id' => $user->id,
            ]);

            $avatarPath = resource_path('seeders/files/avatar.png');

            try {
                $avatarFile->addMedia($avatarPath)
                    ->preservingOriginal()
                    ->toMediaCollection('default');

                $user->update(['avatar_id' => $avatarFile->id]);
            } catch (\Exception $e) {
                $this->command->warn('Failed to seed avatar from local file: '.$e->getMessage());
            }
        }

        $user->refresh();

        $resume = view('seeders.resume', [
            'avatar' => $user->avatar,
            'name' => $user->name,
        ])->render();

        $user->update(['resume' => $resume]);
    }
}
