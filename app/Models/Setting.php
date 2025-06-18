<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
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

    public function settingItems()
    {
        return $this->hasMany(SettingItem::class, 'setting_id', 'uuid');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->order) {
                $model->order = Setting::max('order') + 1;
            }
        });

        static::deleting(function ($model) {
            foreach ($model->settingItems as $item) {
                if ($item->value) {
                    $oriPath = $item->value;
                    $thumbPath = 'thumbs/' . $oriPath;
                    if (Storage::disk('public')->exists($oriPath)) {
                        Storage::disk('public')->delete($oriPath);
                    }
                    if (Storage::disk('public')->exists($thumbPath)) {
                        Storage::disk('public')->delete($thumbPath);
                    }
                }
            }
        });
    }
}
