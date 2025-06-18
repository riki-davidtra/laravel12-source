<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:profiles index')->only('index');
        $this->middleware('can:profiles edit')->only(['edit', 'update']);
    }

    public function index()
    {
        return view('dashboard.pages.profiles.index');
    }

    public function update(Request $request, User $profile)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $profile->id,
            'password'   => 'nullable|string|min:8|confirmed',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        $newAvatarPath = null;
        $newThumbPath  = null;

        try {
            if ($request->hasFile('avatar_url')) {
                $image = $request->file('avatar_url');

                $filename      = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $newAvatarPath = 'users/' . $filename;
                $newThumbPath  = 'thumbs/users/' . $filename;

                $image->storeAs('users', $filename, 'public');

                $manager   = new ImageManager(new Driver());
                $thumbnail = $manager->read($image->getRealPath())->scaleDown(width: 600);
                Storage::disk('public')->put($newThumbPath, (string) $thumbnail->toJpeg());

                $profile->avatar_url = $newAvatarPath;
            }

            $profile->name  = $request->input('name');
            $profile->email = $request->input('email');

            if ($request->filled('password')) {
                $profile->password = bcrypt($request->input('password'));
            }

            $profile->save();

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data updated successfully.'
                ]);
            }

            return redirect()->route('profiles.index')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newAvatarPath && Storage::disk('public')->exists($newAvatarPath)) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            if ($newThumbPath && Storage::disk('public')->exists($newThumbPath)) {
                Storage::disk('public')->delete($newThumbPath);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating data.',
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while updating data.')->withInput();
        }
    }
}
