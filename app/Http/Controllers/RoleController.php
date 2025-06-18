<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:roles index')->only('index');
        $this->middleware('can:roles create')->only(['create', 'store']);
        $this->middleware('can:roles edit')->only(['edit', 'update']);
        $this->middleware('can:roles delete')->only('destroy', 'bulk_delete');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::orderBy('created_at', 'desc');

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
                ->addColumn('permissions', function ($row) {
                    return Str::limit($row->permissions->pluck('name')->join(', '), 50);
                })
                ->addColumn('actions', function ($row) {
                    $editUrl        = route('roles.edit', $row->id);
                    $deleteUrl      = route('roles.destroy', $row->id);
                    $permissionBase = 'roles';

                    return view('components.table.actions', compact('editUrl', 'deleteUrl', 'permissionBase'))->render();
                })
                ->rawColumns(['checkbox', 'permissions', 'actions'])
                ->make(true);
        }

        return view('dashboard.pages.roles.index');
    }

    public function create(Request $request)
    {
        $guard       = $request->get('guard', 'web');
        $permissions = Permission::where('guard_name', $guard)->orderBy('name')->get();

        return view('dashboard.pages.roles.create', compact('permissions', 'guard'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|unique:roles,name',
            'guard_name'    => 'required|string|in:web,api',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => $validated['guard_name'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Data created successfully.');
    }

    public function edit(Role $role)
    {
        $guard           = $role->guard_name;
        $permissions     = Permission::where('guard_name', $guard)->orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('dashboard.pages.roles.edit', compact('role', 'permissions', 'rolePermissions', 'guard'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => 'required|unique:roles,name,' . $role->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Data updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->syncPermissions([]);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Data deleted successfully.');
    }

    public function bulk_delete(Request $request)
    {
        $roleIds = $request->ids;
        $roles   = Role::whereIn('id', $roleIds)->get();

        foreach ($roles as $role) {
            $role->syncPermissions([]);
            $role->delete();
        }

        return redirect()->route('roles.index')->with('success', 'Data deleted successfully.');
    }
}
