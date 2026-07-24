<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Slimani\MediaManager\Form\MediaPicker;
use Slimani\MediaManager\Form\RichEditor\MediaManagerRichContentPlugin;
use Slimani\MediaManager\Models\File;
use Slimani\MediaManager\Models\Folder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(3)
                    ->schema([
                        Group::make([
                            Section::make('General Information')
                                ->icon(Heroicon::User)
                                ->description('Basic user account details.')
                                ->schema([
                                    TextInput::make('name')
                                        ->required(),
                                    TextInput::make('email')
                                        ->label('Email address')
                                        ->email()
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                ])->columns(2),

                            Section::make('Resume')
                                ->icon(Heroicon::DocumentText)
                                ->description('Detailed professional resume.')
                                ->schema([
                                    RichEditor::make('resume')
                                        ->label('Resume Content')
                                        ->columnSpanFull()
                                        ->plugins([
                                            MediaManagerRichContentPlugin::make()
                                                ->acceptedFileTypes(['image/*']),
                                        ]),
                                ]),

                            Section::make('Security')
                                ->icon(Heroicon::Key)
                                ->description('Update user password.')
                                ->schema([
                                    TextInput::make('password')
                                        ->password()
                                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                        ->dehydrated(fn ($state) => filled($state))
                                        ->required(fn (string $context): bool => $context === 'create')
                                        ->columnSpanFull(),
                                ]),
                        ])->columnSpan(2),

                        Group::make([
                            Section::make('Profile Media')
                                ->icon(Heroicon::Photo)
                                ->description('Avatars and documents.')
                                ->schema([
                                    MediaPicker::make('avatar_id')
                                        ->label('Avatar')
                                        ->avatar()
                                        ->hintActions([
                                            Action::make('importImageFromUrl')
                                                ->label('Import from URL')
                                                ->icon(Heroicon::Link)
                                                ->schema([
                                                    TextInput::make('url')
                                                        ->label('Image URL')
                                                        ->url()
                                                        ->required()
                                                        ->placeholder('https://example.com/image.jpg'),
                                                    SelectTree::make('folder_id')
                                                        ->label('Destination folder')
                                                        ->query(Folder::query()->orderBy('name'), 'name', 'parent_id')
                                                        ->prepend([
                                                            'name' => 'Root',
                                                            'value' => 0,
                                                        ])
                                                        ->enableBranchNode()
                                                        ->withCount()
                                                        ->searchable(),
                                                ])
                                                ->action(function (MediaPicker $component, array $data): void {
                                                    $file = File::create([
                                                        'name' => pathinfo(parse_url($data['url'], PHP_URL_PATH) ?: 'image', PATHINFO_FILENAME) ?: 'image',
                                                        'uploaded_by_user_id' => auth()->id(),
                                                        'folder_id' => $data['folder_id'] ?? null,
                                                    ]);

                                                    try {
                                                        $media = $file->addMediaFromUrl(
                                                            $data['url'],
                                                            'image/avif',
                                                            'image/gif',
                                                            'image/jpeg',
                                                            'image/png',
                                                            'image/webp',
                                                        )->toMediaCollection('default', filament('media-manager')->getDisk());

                                                        $file->update([
                                                            'name' => pathinfo($media->file_name, PATHINFO_FILENAME),
                                                            'size' => $media->size,
                                                            'mime_type' => $media->mime_type,
                                                            'extension' => $media->extension,
                                                            'width' => $media->getCustomProperty('width'),
                                                            'height' => $media->getCustomProperty('height'),
                                                        ]);

                                                        $component->state((string) $file->id);

                                                        Notification::make()
                                                            ->title('Image imported')
                                                            ->success()
                                                            ->send();
                                                    } catch (\Throwable $exception) {
                                                        $file->delete();

                                                        Notification::make()
                                                            ->title('Image could not be imported')
                                                            ->body($exception->getMessage())
                                                            ->danger()
                                                            ->send();
                                                    }
                                                }),
                                        ]),
                                    MediaPicker::make('cv_id')
                                        ->label('CV / Resume')
                                        ->directory('User/Documents')
                                        ->collection('documents'),
                                    MediaPicker::make('documents')
                                        ->label('Additional Documents')
                                        ->relationship('documents')
                                        ->multiple()
                                        ->directory('User/Attachments'),
                                ]),

                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
