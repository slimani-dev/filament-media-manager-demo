<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folder1 = \Slimani\MediaManager\Models\Folder::create(['name' => 'Project Images']);
        $folder2 = \Slimani\MediaManager\Models\Folder::create(['name' => 'Documents']);
        $subfolder = \Slimani\MediaManager\Models\Folder::create(['name' => 'Logo', 'parent_id' => $folder1->id]);

        $file1 = \Slimani\MediaManager\Models\File::create([
            'name' => 'Main Banner',
            'folder_id' => $folder1->id,
            'size' => 1024,
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'uploaded_by_user_id' => 1,
        ]);

        try {
            $file1->addMediaFromUrl('https://picsum.photos/1200/800')
                ->toMediaCollection('default');
        } catch (\Exception $e) {
            // Fallback if network is unavailable
        }

        $file2 = \Slimani\MediaManager\Models\File::create([
            'name' => 'Project Specs',
            'folder_id' => $folder2->id,
            'size' => 2048,
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'uploaded_by_user_id' => 1,
        ]);

        // Note: For PDF we might just use a placeholder text file renamed if we want a real file, 
        // or just skip if we don't have a reliable PDF URL.
    }
}
