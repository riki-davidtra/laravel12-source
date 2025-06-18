<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
        ];
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public $incrementing = true;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }

    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isDirty('avatar_url')) {
                $oldAvatar = $model->getOriginal('avatar_url');
                if ($oldAvatar && $oldAvatar !== $model->avatar_url) {
                    Storage::disk('public')->delete($oldAvatar);
                    $oldThumb = str_replace('users/', 'thumbs/users/', $oldAvatar);
                    Storage::disk('public')->delete($oldThumb);
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->avatar_url) {
                Storage::disk('public')->delete($model->avatar_url);
                $thumbPath = str_replace('users/', 'thumbs/users/', $model->avatar_url);
                Storage::disk('public')->delete($thumbPath);
            }
        });
    }
}
