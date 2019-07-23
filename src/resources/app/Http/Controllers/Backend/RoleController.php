<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!auth()->user()->getRoleNames()->contains('super-admin'))
                abort(403, 'Access denied');
            return $next($request);
        });

    }

    /**
     * Show the application roles index.
     */
    public function index(Request $request)
    {
        $sortDirection = $request->input('sort_dir') ?: 'asc';
        $sortColumn = $request->input('sort') ?: 'created_at';
        return view('backend.roles.index', [
            'roles' => Role::orderBy($sortColumn, $sortDirection)->paginate(10)
        ]);
    }

    /**
     * Display the specified resource edit form.
     */
    public function edit(Role $role)
    {
        return view('backend.roles.edit', [
            'role' => $role, 
            'groupedPermissions' => Permission::all()->map(function ($perm) {
                $perm->name = str_replace('_', ' ', $perm->name);
                return $perm;
            })->groupBy(function ($perm) { 
                foreach (array_keys(app('laragen')->getModules()) as $module) {
                    if(Str::contains($perm->name, str_replace('_', ' ', $module)))
                        return Str::title(str_replace('_', ' ', $module));
                }
            }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.roles.create', [
            'permissions' => Permission::all(),
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $role = Role::create($request->all());

            return redirect()->route('backend.roles.edit', $role)->withSuccess(__('roles.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($request->has("permissions")) {
            $role->permissions()->sync($request->input("permissions"));
        }
        $role->update(['name' => $request->name]);
        return redirect()->route('backend.roles.edit', $role)->withSuccess(__('Role successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role  $role)
    {
        $role->delete();

        return redirect()->route('backend.roles.index')->withSuccess(__('roles.deleted'));
    }
}