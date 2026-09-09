<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
public function index(Request $request)
{
    $request->merge([
        'start'  => $request->start ?? 0,
        'length' => $request->length ?? 10,
    ]);

    $query = Role::latest();

    if ($request->ajax()) {

        // Base query clone (IMPORTANT)
        $baseQuery = clone $query;

        if (!empty($request->search['value'])) {

            $search = $request->search['value'];

            $query->where('name', 'like', "%{$search}%");
        }

        $total = $baseQuery->count();

        $filtered = $query->count();

        $roles = $query->skip($request->start ?? 0)
            ->take($request->length ?? 10)
            ->get();

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $total,
            "recordsFiltered" => $filtered,
            "data"            => $roles,
        ]);
    }

    $roles = Role::latest()->get();

    return view('roles.index', compact('roles'));
}

    public function store(Request $request)
    {
        $request->merge([
            'name' => trim($request->name)
        ]);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->where(function ($query) use ($request) {
                    $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
                }),
            ],
        ], [
            'name.required' => 'Role name is required.',
            'name.unique' => 'This role already exists.',
        ]);



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Role::create(['name' => $request->name]);

        return response()->json(['success' => 'Role created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'name' => trim($request->name)
        ]);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->ignore($id)
                    ->where(function ($query) use ($request) {
                        $query->whereRaw('LOWER(name) = ?', [strtolower($request->name)]);
                    }),
            ],
        ], [
            'name.required' => 'Role name is required.',
            'name.unique' => 'This role already exists.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Role::findOrFail($id)->update(['name' => $request->name]);

        return response()->json(['success' => 'Role updated successfully.']);
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();
        return response()->json(['success' => 'Role deleted successfully.']);
    }
}
