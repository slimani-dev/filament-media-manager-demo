<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Slimani\MediaManager\Models\File;
use Slimani\MediaManager\Models\Folder;

class MediaShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $folders = [
            'Images' => Folder::firstOrCreate(['name' => 'Showcase Images']),
            'Videos' => Folder::firstOrCreate(['name' => 'Showcase Videos']),
            'Audio' => Folder::firstOrCreate(['name' => 'Showcase Audio']),
            'Documents' => Folder::firstOrCreate(['name' => 'Showcase Documents']),
        ];

        $mediaItems = [
            [
                'name' => 'Stunning Landscape',
                'url' => 'https://picsum.photos/1200/800',
                'folder' => 'Images',
                'ext' => 'jpg',
                'mime' => 'image/jpeg',
            ],
            [
                'name' => 'Sample PDF Document',
                'url' => 'https://upload.wikimedia.org/wikipedia/commons/d/d3/Test.pdf',
                'folder' => 'Documents',
                'ext' => 'pdf',
                'mime' => 'application/pdf',
            ],
            [
                'name' => 'Standard Video File',
                'url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'folder' => 'Videos',
                'ext' => 'mp4',
                'mime' => 'video/mp4',
            ],
            [
                'name' => 'Relaxing Audio Track',
                'url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'folder' => 'Audio',
                'ext' => 'mp3',
                'mime' => 'audio/mpeg',
            ],
            [
                'name' => 'Sample DOCX File',
                'url' => 'https://calibre-ebook.com/downloads/demos/demo.docx',
                'folder' => 'Documents',
                'ext' => 'docx',
                'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
        ];

        foreach ($mediaItems as $item) {
            $file = File::create([
                'name' => $item['name'],
                'folder_id' => $folders[$item['folder']]->id,
                'size' => 0, // Will be updated by media library if possible, otherwise placeholder
                'extension' => $item['ext'],
                'mime_type' => $item['mime'],
                'uploaded_by_user_id' => $user->id,
            ]);

            try {
                $file->addMediaFromUrl($item['url'])
                    ->toMediaCollection('default');
            } catch (\Exception $e) {
                $this->command->warn("Failed to seed media from URL: {$item['url']} for {$item['name']}. Error: {$e->getMessage()}");
            }
        }
    }
}
