<?php

namespace App\Observers;

use App\Models\SettingItem;
use Illuminate\Support\Facades\Storage;

class SettingItemObserver
{
    public function updating(SettingItem $settingItem): void
    {
        if ($settingItem->isDirty('value')) {
            if ($settingItem->getOriginal('value') && Storage::disk('public')->exists($settingItem->getOriginal('value'))) {
                Storage::disk('public')->delete($settingItem->getOriginal('value'));
            }

            if ($settingItem->getOriginal('value') && Storage::disk('public')->exists('thumbs/' . $settingItem->getOriginal('value'))) {
                Storage::disk('public')->delete('thumbs/' . $settingItem->getOriginal('value'));
            }
        }
    }

    public function deleting(SettingItem $settingItem): void
    {
        if ($settingItem->value && Storage::disk('public')->exists($settingItem->value)) {
            Storage::disk('public')->delete($settingItem->value);
        }

        if ($settingItem->value && Storage::disk('public')->exists('thumbs/' . $settingItem->value)) {
            Storage::disk('public')->delete('thumbs/' . $settingItem->value);
        }
    }
}
