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

        // 1. Define Simplified Folders
        $folders = [
            'Images' => Folder::firstOrCreate(['name' => 'Images']),
            'Videos' => Folder::firstOrCreate(['name' => 'Videos']),
            'Documents' => Folder::firstOrCreate(['name' => 'Documents']),
        ];

        // 2. Define Media Items (Folders & Root)
        $mediaItems = [
            // --- FOLDER: IMAGES (6 in folder) ---
            ['name' => 'Mountain Lake Reflection', 'file' => 'mountain.jpg', 'folder' => 'Images', 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Forest Pathway', 'file' => 'forest.jpg', 'folder' => 'Images', 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Beach Sunset', 'file' => 'beach.jpg', 'folder' => 'Images', 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'City Skyline Night', 'file' => 'city.jpg', 'folder' => 'Images', 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Golden Gate Bridge', 'file' => 'bridge.jpg', 'folder' => 'Images', 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Autumn Leaves', 'file' => 'leaves.jpg', 'folder' => 'Images', 'ext' => 'jpg', 'mime' => 'image/jpeg'],


            // --- FOLDER: DOCUMENTS (1 in folder) ---
            ['name' => 'Research Paper Sample', 'file' => 'research-paper.pdf', 'folder' => 'Documents', 'ext' => 'pdf', 'mime' => 'application/pdf'],

            // --- ROOT FOLDER (No folder specified) ---
            // 4 Images in Root
            ['name' => 'Desert Dunes', 'file' => 'desert.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Snowy Peak', 'file' => 'snow.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Industrial Workspace', 'file' => 'workspace.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'River Canyon', 'file' => 'canyon.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],

            // Extra Images in Root
            ['name' => 'City View 1', 'file' => 'img-city1.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'City View 2', 'file' => 'img-city2.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Nature Scene 1', 'file' => 'img-nature1.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Nature Scene 2', 'file' => 'img-nature2.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['name' => 'Modern Office', 'file' => 'img-office1.jpg', 'folder' => null, 'ext' => 'jpg', 'mime' => 'image/jpeg'],

            // 1 PDF in Root
            ['name' => 'User Manual PDF', 'file' => 'user-manual.pdf', 'folder' => null, 'ext' => 'pdf', 'mime' => 'application/pdf'],
            ['name' => 'Standard PDF Sample', 'file' => 'pdf-sample1.pdf', 'folder' => null, 'ext' => 'pdf', 'mime' => 'application/pdf'],
            ['name' => 'Test Document PDF', 'file' => 'test.pdf', 'folder' => null, 'ext' => 'pdf', 'mime' => 'application/pdf'],

            // 1 DOCX in Root
            ['name' => 'Project Proposal DOCX', 'file' => 'project-proposal.docx', 'folder' => null, 'ext' => 'docx', 'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],

            // CSVs in Root
            ['name' => 'Population Data CSV', 'file' => 'csv-population.csv', 'folder' => null, 'ext' => 'csv', 'mime' => 'text/csv'],
            ['name' => 'Sample Data CSV', 'file' => 'csv-sample1.csv', 'folder' => null, 'ext' => 'csv', 'mime' => 'text/csv'],

            // Videos in Root
            ['name' => 'Lamborghini Revuelto Video', 'file' => 'Lamborghini Revuelto – From Now On.webm', 'folder' => null, 'ext' => 'webm', 'mime' => 'video/webm'],
            ['name' => 'McLaren P1 Video', 'file' => 'McLaren P1 on Backroads.webm', 'folder' => null, 'ext' => 'webm', 'mime' => 'video/webm'],

            // Others
            ['name' => 'Robots Configuration', 'file' => 'txt-robots.txt', 'folder' => null, 'ext' => 'txt', 'mime' => 'text/plain'],
        ];

        foreach ($mediaItems as $item) {
            // Check if file already exists to avoid duplicates if run multiple times
            $existingFile = File::where('name', $item['name'])
                ->where('uploaded_by_user_id', $user->id)
                ->first();

            if ($existingFile) {
                continue;
            }

            $folderId = $item['folder'] ? ($folders[$item['folder']]->id ?? null) : null;

            $file = File::create([
                'name' => $item['name'],
                'folder_id' => $folderId,
                'size' => 0,
                'extension' => $item['ext'],
                'mime_type' => $item['mime'],
                'uploaded_by_user_id' => $user->id,
            ]);

            try {
                $filePath = resource_path('seeders/files/'.$item['file']);

                $media = $file->addMedia($filePath)
                    ->preservingOriginal()
                    ->toMediaCollection('default');

                // Update file model with actual media information
                $file->update([
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'extension' => $media->extension,
                ]);

            } catch (\Exception $e) {
                $this->command->warn("Failed to seed media from local file: {$item['file']} for {$item['name']}. Error: {$e->getMessage()}");
                $file->delete(); // Cleanup if failed
            }
        }
    }
}
