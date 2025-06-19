<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:users index')->only('index');
        $this->middleware('can:users create')->only(['create', 'store']);
        $this->middleware('can:users edit')->only(['edit', 'update']);
        $this->middleware('can:users delete')->only('destroy', 'bulk_delete');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('checkbox', function ($row) {
                    return view('components.table.checkbox', ['value' => $row->id])->render();
                })
                ->addColumn('avatar_url', function ($user) {
                    return view('components.table.thumbnail', [
                        'path' => $user->avatar_url,
                        'attributes' => new ComponentAttributeBag(['class' => 'rounded-full'])
                    ])->render();
                })
                ->addColumn('roles', function ($row) {
                    return $row->getRoleNames()->implode(', ');
                })
                ->addColumn('actions', function ($row) {
                    $editUrl        = route('users.edit', $row->uuid);
                    $deleteUrl      = route('users.destroy', $row->uuid);
                    $permissionBase = 'users';

                    return view('components.table.actions', compact('editUrl', 'deleteUrl', 'permissionBase'))->render();
                })
                ->rawColumns(['checkbox', 'avatar_url', 'roles', 'actions'])
                ->make(true);
        }

        return view('dashboard.pages.users.index');
    }

    public function create()
    {
        $roleOptions = Role::pluck('name', 'name')->toArray();

        return view('dashboard.pages.users.create', compact('roleOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'roles'      => 'nullable|array',
            'roles.*'    => 'exists:roles,name',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        $newAvatarPath = null;
        $newThumbPath  = null;

        try {
            if ($request->hasFile('avatar_url')) {
                $image    = $request->file('avatar_url');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

                $newAvatarPath = 'users/' . $filename;
                $newThumbPath  = 'thumbs/users/' . $filename;

                $image->storeAs('users', $filename, 'public');

                $manager   = new ImageManager(new Driver());
                $thumbnail = $manager->read($image->getRealPath())->scaleDown(width: 600);
                Storage::disk('public')->put($newThumbPath, (string) $thumbnail->toJpeg());
            }

            $user = User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'avatar_url' => $newAvatarPath,
            ]);

            if ($request->filled('roles')) {
                $user->assignRole($request->roles);
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'Data created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newAvatarPath && Storage::disk('public')->exists($newAvatarPath)) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            if ($newThumbPath && Storage::disk('public')->exists($newThumbPath)) {
                Storage::disk('public')->delete($newThumbPath);
            }

            return redirect()->back()->with('error', 'An error occurred while saving data.')->withInput();
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(User $user)
    {
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'Data not found.');
        }

        $roleOptions   = Role::pluck('name', 'name')->toArray();
        $selectedRoles = $user->roles->pluck('name')->toArray();

        return view('dashboard.pages.users.edit', compact('user', 'roleOptions', 'selectedRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'   => 'nullable|string|min:8|confirmed',
            'roles'      => 'nullable|array',
            'roles.*'    => 'exists:roles,name',
            'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        $newAvatarPath = null;
        $newThumbPath  = null;

        try {
            if ($request->hasFile('avatar_url')) {
                $image    = $request->file('avatar_url');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

                $newAvatarPath = 'users/' . $filename;
                $newThumbPath  = 'thumbs/users/' . $filename;

                $image->storeAs('users', $filename, 'public');

                $manager   = new ImageManager(new Driver());
                $thumbnail = $manager->read($image->getRealPath())->scaleDown(width: 600);

                Storage::disk('public')->put($newThumbPath, (string) $thumbnail->toJpeg());

                $user->avatar_url = $newAvatarPath;
            }

            $user->name  = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            $user->syncRoles($validated['roles'] ?? []);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'Data updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newAvatarPath && Storage::disk('public')->exists($newAvatarPath)) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            if ($newThumbPath && Storage::disk('public')->exists($newThumbPath)) {
                Storage::disk('public')->delete($newThumbPath);
            }

            return redirect()->back()->with('error', 'An error occurred while updating data.')->withInput();
        }
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Data deleted successfully.');
    }

    public function bulk_delete(Request $request)
    {
        User::whereIn('id', $request->ids)->chunk(50, function ($users) {
            foreach ($users as $user) {
                $user->delete();
            }
        });

        return redirect()->route('users.index')->with('success', 'Data deleted successfully.');
    }
}
