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

        \Slimani\MediaManager\Models\File::create([
            'name' => 'Main Banner',
            'folder_id' => $folder1->id,
            'size' => 1024,
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'uploaded_by_user_id' => 1,
        ]);

        \Slimani\MediaManager\Models\File::create([
            'name' => 'Project Specs',
            'folder_id' => $folder2->id,
            'size' => 2048,
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'uploaded_by_user_id' => 1,
        ]);
    }
}
