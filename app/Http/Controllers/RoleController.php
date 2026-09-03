<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
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
