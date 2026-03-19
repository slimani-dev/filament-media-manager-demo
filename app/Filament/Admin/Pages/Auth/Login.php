<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    protected Width|string|null $maxContentWidth = '7xl';

    public function form(Schema $schema): Schema
    {
        $impersonateActions = [];

        if (app()->isLocal()) {
            /** @var Collection<int, User> $users */
            $users = User::limit(3)->get()->chunk(3);

            foreach ($users as $group) {
                $impersonateActions[] = ActionGroup::make(
                    $group->map(fn (User $user) => Action::make("login_as_{$user->id}")
                        ->label('Login as '.$user->name)
                        ->action(function () use ($user) {
                            Auth::login($user);

                            return redirect(Filament::getCurrentPanel()->getUrl());
                        })
                    )->all()
                )->buttonGroup();
            }
        }

        return $schema
            ->components([
                Section::make('One-Click Login')
                    ->description('Simply click one of the buttons below to access the demo as a pre-configured user. No password required.')
                    ->icon('heroicon-o-cursor-arrow-rays')
                    ->schema([
                        ...$impersonateActions,
                    ]),

                Callout::make('Periodic Resets')
                    ->description('This demo environment is public and resets every hour. All changes will be lost.')
                    ->warning()
                    ->icon(Heroicon::OutlinedClock),

                Callout::make('Privacy Warning')
                    ->description('Do not upload sensitive or personal data. This is a shared environment.')
                    ->danger(),

                Callout::make('Usage Policy')
                    ->description('Please do not abuse the system. Be kind to other users.')
                    ->info(),
            ]);

    }

    public function getFormContentComponent(): Component
    {

        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('authenticate')
            ->footer([]);
    }
}
