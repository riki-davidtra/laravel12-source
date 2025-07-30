<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        // $this->middleware('can:permissions index')->only('index');
        // $this->middleware('can:permissions create')->only(['create', 'store']);
        // $this->middleware('can:permissions edit')->only(['edit', 'update']);
        // $this->middleware('can:permissions delete')->only('destroy', 'bulk_delete');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::orderBy('created_at', 'desc');

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
                ->addColumn('actions', function ($row) {
                    $editUrl        = route('permissions.edit', $row->id);
                    $deleteUrl      = route('permissions.destroy', $row->id);
                    $permissionBase = 'permissions';

                    return view('components.table.actions', compact('editUrl', 'deleteUrl', 'permissionBase'))->render();
                })
                ->rawColumns(['checkbox', 'actions'])
                ->make(true);
        }

        return view('dashboard.pages.permissions.index');
    }

    public function create()
    {
        return view('dashboard.pages.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|unique:permissions,name',
            'guard_name' => 'required|string|in:web,api',
        ]);

        Permission::create([
            'name'       => $validated['name'],
            'guard_name' => $validated['guard_name'],
        ]);

        return redirect()->route('permissions.index')->with('success', 'Data created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Permission $permission)
    {
        return view('dashboard.pages.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'       => 'required|unique:permissions,name,' . $permission->id,
            'guard_name' => 'required|string|in:web,api',
        ]);

        $permission->update([
            'name'       => $validated['name'],
            'guard_name' => $validated['guard_name']
        ]);

        return redirect()->route('permissions.index')->with('success', 'Data updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Data deleted successfully.');
    }

    public function bulk_delete(Request $request)
    {
        $ids         = $request->ids;
        $permissions = Permission::whereIn('id', $ids)->get();

        foreach ($permissions as $permission) {
            $permission->delete();
        }

        return redirect()->route('permissions.index')->with('success', 'Data deleted successfully.');
    }
}
