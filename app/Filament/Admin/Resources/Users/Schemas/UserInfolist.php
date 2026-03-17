<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Slimani\MediaManager\Infolists\Components\MediaFileEntry;
use Slimani\MediaManager\Infolists\Components\MediaImageEntry;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Grid::make(3)
                    ->schema([
                        Group::make([
                            Section::make('General Information')
                                ->icon(Heroicon::User)
                                ->description('Basic user account details.')
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('email')
                                        ->label('Email address'),
                                    TextEntry::make('email_verified_at')
                                        ->label('Email Verified At')
                                        ->dateTime(),
                                ])->columns(2),
                        ])->columnSpan(2),

                        Group::make([
                            Section::make('Profile Media')
                                ->icon(Heroicon::Photo)
                                ->description('Avatars and documents.')
                                ->schema([
                                    MediaImageEntry::make('avatar_id')
                                        ->label('Avatar')
                                        ->circular(),
                                    MediaImageEntry::make('cv_id')
                                        ->imageWidth('100%')
                                        ->imageHeight('auto')
                                        ->label('CV / Resume'),
                                    MediaFileEntry::make('documents')
                                        ->label('Additional Documents'),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }
}
