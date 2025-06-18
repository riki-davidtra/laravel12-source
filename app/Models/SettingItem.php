<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingItem extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public $incrementing = true;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class, 'setting_id', 'uuid');
    }

    protected static function booted()
    {
        static::updating(function ($model) {
            if ($model->isDirty('value')) {
                $oriPathOld = $model->getOriginal('value');
                $thumbPathOld = 'thumbs/' . $oriPathOld;
                $oriPathNew = $model->value;
                if ($oriPathOld && $oriPathOld !== $oriPathNew) {
                    if (Storage::disk('public')->exists($oriPathOld)) {
                        Storage::disk('public')->delete($oriPathOld);
                    }
                    if (Storage::disk('public')->exists($thumbPathOld)) {
                        Storage::disk('public')->delete($thumbPathOld);
                    }
                }
            }
        });

        static::deleting(function ($model) {
            if ($model->value) {
                $oriPath = $model->value;
                $thumbPath = 'thumbs/' . $oriPath;
                if (Storage::disk('public')->exists($oriPath)) {
                    Storage::disk('public')->delete($oriPath);
                }
                if (Storage::disk('public')->exists($thumbPath)) {
                    Storage::disk('public')->delete($thumbPath);
                }
            }
        });
    }
}
