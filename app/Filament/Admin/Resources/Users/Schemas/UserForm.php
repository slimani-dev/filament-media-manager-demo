<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Slimani\MediaManager\Form\MediaPicker;
use Slimani\MediaManager\Form\RichEditor\MediaManagerRichContentPlugin;

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
                                        ->avatar(),
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
