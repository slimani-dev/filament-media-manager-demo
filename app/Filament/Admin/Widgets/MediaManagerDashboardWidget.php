<?php

namespace App\Filament\Admin\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Widgets\Widget;
use Illuminate\Support\Number;
use Slimani\MediaManager\Models\File;
use Slimani\MediaManager\Models\Folder;

class MediaManagerDashboardWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms, InteractsWithSchemas {
        InteractsWithForms::getCachedSchemas insteadof InteractsWithSchemas;
        InteractsWithSchemas::getCachedSchemas as getBaseCachedSchemas;
    }

    protected string $view = 'filament.admin.widgets.media-manager-dashboard-widget';

    protected int|string|array $columnSpan = 'full';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Section::make('Filament Media Manager')
                    ->description('Overview and stats for your media library')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('files_count')
                                    ->label('Total Files')
                                    ->state(File::count())
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-document-duplicate')
                                    ->badge(),
                                TextEntry::make('folders_count')
                                    ->label('Total Folders')
                                    ->state(Folder::count())
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-folder')
                                    ->badge(),
                                TextEntry::make('storage_size')
                                    ->label('Storage Used')
                                    ->state(Number::fileSize(File::sum('size') ?? 0))
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->size(TextSize::Large)
                                    ->icon('heroicon-o-circle-stack')
                                    ->badge(),
                            ]),

                        Section::make('Key Features')
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('feature_folders')
                                            ->state('Folder-based organization')
                                            ->helperText('Organize your media into hierarchical folders.')
                                            ->icon('heroicon-o-folder'),
                                        TextEntry::make('feature_tags')
                                            ->state('Taggable media')
                                            ->helperText('Add tags to your files for easier searching and filtering.')
                                            ->icon('heroicon-o-tag'),
                                        TextEntry::make('feature_native')
                                            ->state('Native Filament integration')
                                            ->helperText('Built specifically for Filament with support for forms, tables, and actions.')
                                            ->icon('heroicon-o-puzzle-piece'),
                                        TextEntry::make('feature_rich_editor')
                                            ->state('Rich Text Editor Integration')
                                            ->helperText('Insert images directly from your media library into RichEditor.')
                                            ->icon('heroicon-o-pencil-square'),

                                    ]),
                            ]),
                    ])
                    ->footerActions([
                        Action::make('open_media_manager')
                            ->label('Open Media Manager')
                            ->icon('heroicon-o-arrow-top-right-on-square')
                            ->url(fn () => route('filament.admin.pages.media-manager'))
                            ->button()
                            ->color('primary'),
                        Action::make('documentation')
                            ->label('Documentation')
                            ->icon('heroicon-o-book-open')
                            ->url('https://github.com/slimani-dev/filament-media-manager')
                            ->button()
                            ->iconPosition('before')
                            ->color('gray'),
                    ]),

            ]);
    }
}
