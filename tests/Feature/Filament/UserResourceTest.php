<?php

use App\Filament\Admin\Resources\Users\Pages\ViewUser;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\be;

it('can render user view page', function () {
    $user = User::factory()->create();

    be($user);

    Livewire::test(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->email);
});
