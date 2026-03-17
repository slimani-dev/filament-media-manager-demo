<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Slimani\MediaManager\Models\File;
use Slimani\MediaManager\Models\Folder;

class FilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folder1 = Folder::create(['name' => 'Project Images']);
        $folder2 = Folder::create(['name' => 'Documents']);
        $subfolder = Folder::create(['name' => 'Logo', 'parent_id' => $folder1->id]);

        $file1 = File::create([
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

        $file2 = File::create([
            'name' => 'Project Specs',
            'folder_id' => $folder2->id,
            'size' => 2048,
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'uploaded_by_user_id' => 1,
        ]);

        try {
            $file2->addMediaFromUrl('https://upload.wikimedia.org/wikipedia/commons/d/d3/Test.pdf')
                ->toMediaCollection('default');
        } catch (\Exception $e) {
            // Fallback if network is unavailable
        }
    }
}
