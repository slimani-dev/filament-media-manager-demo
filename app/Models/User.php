<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Slimani\MediaManager\Concerns\InteractsWithMediaFiles;
use Slimani\MediaManager\Form\RichEditor\FileAttachmentProviders\MediaManagerFileAttachmentProvider;

class User extends Authenticatable implements HasAvatar, HasRichContent
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, InteractsWithMediaFiles, InteractsWithRichContent, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_id',
        'cv_id',
        'resume',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('resume')
            ->fileAttachmentProvider(
                MediaManagerFileAttachmentProvider::make()
                    ->collection('preview')
                    ->directory('User/Resumes')
            );
    }

    public function avatar(): BelongsTo
    {
        return $this->mediaFile('avatar_id');
    }

    public function cv(): BelongsTo
    {
        return $this->mediaFile('cv_id');
    }

    public function documents(): MorphToMany
    {
        return $this->mediaFiles('documents');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar?->getUrl('thumb') ?? $this->avatar?->getUrl();
    }
}
