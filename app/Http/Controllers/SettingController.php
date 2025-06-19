<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:settings index')->only('index');
        $this->middleware('can:settings edit')->only(['edit', 'update_all']);
    }

    public function index()
    {
        $settings = Setting::orderBy('order', 'asc')->get();
        return view('dashboard.pages.settings.index', compact('settings'));
    }

    public function update_all(Request $request)
    {
        $settings = Setting::with('settingItems')->get();

        $rules = [];

        foreach ($settings as $setting) {
            foreach ($setting->settingItems as $item) {
                $key = $item->key;

                switch ($item->type) {
                    case 'email':
                        $rules[$key] = 'nullable|email';
                        break;
                    case 'url':
                        $rules[$key] = 'nullable|url';
                        break;
                    case 'number':
                        $rules[$key] = 'nullable|numeric';
                        break;
                    case 'file':
                        $rules[$key] = 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp,ico,tiff,psd,ai,eps,heic,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,json,xml,mp4,mov,mkv,avi,wmv,webm,mp3,wav,ogg,flac,zip,rar,7z,tar,gz,apk|max:5120';
                        break;
                    default:
                        $rules[$key] = 'nullable|string';
                        break;
                }
            }
        }

        $validated = $request->validate($rules);

        foreach ($settings as $setting) {
            foreach ($setting->settingItems as $item) {
                $key = $item->key;

                if ($request->has($key) || ($item->type === 'file' && $request->hasFile($key))) {
                    if ($item->type === 'file') {
                        $path = $request->file($key)->store('settings', 'public');
                        $item->value = $path;
                    } else {
                        $value = $request->input($key);

                        if (str($item->type)->contains('multiple') && is_array($value)) {
                            $value = json_encode($value);
                        }

                        $item->value = $value;
                    }

                    $item->save();
                }
            }
        }

        return back()->with('success', 'All settings updated successfully.');
    }
}
