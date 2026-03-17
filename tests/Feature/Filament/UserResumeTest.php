<?php

use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\be;

it('can see resume field in edit page', function () {
    $user = User::factory()->create();

    be($user);

    Livewire::test(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $user->name,
            'resume' => $user->resume ?? '<p></p>',
        ]);
});

it('can save resume content', function () {
    $user = User::factory()->create();

    be($user);

    $resumeContent = '<p>This is my professional resume.</p>';

    Livewire::test(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([
            'resume' => $resumeContent,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->refresh()->resume)->toBe($resumeContent);
});
