<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    public function getFormContentComponent(): Component
    {
        $impersonateActions = [];

        if (app()->isLocal()) {
            /** @var Collection<int, User> $users */
            $users = User::limit(9)->get()->chunk(3);

            foreach ($users as $group) {
                $impersonateActions[] = ActionGroup::make(
                    $group->map(fn (User $user) => Action::make("login_as_{$user->id}")
                        ->label($user->name)
                        ->action(function () use ($user) {
                            Auth::login($user);

                            return redirect(Filament::getCurrentPanel()->getUrl());
                        })
                    )->all()
                )->buttonGroup();
            }
        }

        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('authenticate')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),

                ...$impersonateActions,
            ]);
    }
}
