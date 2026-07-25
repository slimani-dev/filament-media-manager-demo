<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Slimani\MediaManager\Models\File;
use Slimani\MediaManager\Models\Folder;

class TenantMediaSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->value('id');
        $sourceFile = resource_path('seeders/files/avatar.png');

        foreach (Tenant::query()->get() as $tenant) {
            $tenantId = (string) $tenant->getKey();
            $folder = Folder::withoutEvents(function () use ($tenantId): Folder {
                $folder = Folder::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('name', 'Tenant Media')
                    ->whereNull('parent_id')
                    ->first();

                if ($folder) {
                    return $folder;
                }

                $folder = new Folder(['name' => 'Tenant Media']);
                $folder->forceFill(['tenant_id' => $tenantId]);
                $folder->save();

                return $folder;
            });

            File::withoutEvents(function () use ($tenant, $tenantId, $folder, $userId, $sourceFile): void {
                $file = File::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('name', $tenant->name.' Banner')
                    ->first();

                if (! $file) {
                    $file = new File([
                        'name' => $tenant->name.' Banner',
                        'folder_id' => $folder->getKey(),
                        'uploaded_by_user_id' => $userId,
                        'size' => 0,
                        'extension' => 'png',
                        'mime_type' => 'image/png',
                    ]);
                    $file->forceFill(['tenant_id' => $tenantId]);
                    $file->save();
                }

                if ($file->hasMedia('default') || ! is_file($sourceFile)) {
                    return;
                }

                $media = $file->addMedia($sourceFile)
                    ->preservingOriginal()
                    ->usingFileName(str($tenant->name)->slug().'-banner.png')
                    ->toMediaCollection('default');

                $file->forceFill([
                    'size' => $media->size,
                    'extension' => $media->extension,
                    'mime_type' => $media->mime_type,
                ])->save();
            });
        }
    }
}
