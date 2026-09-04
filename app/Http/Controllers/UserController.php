<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Notifications\UserCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $request->merge([
            'start' => $request->start ?? 0,
            'length' => $request->length ?? 10,
        ]);
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        $query = User::with(['role', 'agency'])
            ->where('id', '!=', $authUser->id)
            ->latest();

        if (in_array($roleName, ['mis user', 'admin'])) {
            // Only users of the same agency
            $query->where('agency_id', $authUser->agency_id);

        } elseif (!empty(session('agency_ids', []))) {
            // Superadmin with session filter
            $query->whereIn('agency_id', session('agency_ids'));
        }
            // else superadmin with no filter → sees all
        if ($request->ajax()) {

            // Base query clone (IMPORTANT)
            $baseQuery = clone $query;

            if (!empty($request->search['value'])) {

                $search = $request->search['value'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $total = $baseQuery->count();

            $filtered = $query->count();
            $users = $query->skip($request->start ?? 0)
                ->take($request->length ?? 10)
                ->with(['role', 'agency'])
                ->get();

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $total,
                "recordsFiltered" => $filtered,
                "data" => $users
            ]);
        }

        $users    = $query->get();
        $roles    = Role::all();
        $agencies = Agency::all();
        $products = Product::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'agencies', 'authUser', 'products'));
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);

        $rules = [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required',
            'role_id'       => 'required',
            'product_id'    => ['required', 'array', 'min:1'],
            'product_id.*'  => ['exists:products,id'],

            'date_of_birth' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],
            'city'          => 'required',
            'state'         => 'required',
            'zip'           => 'required',
            'address'       => 'required',
            'profile'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // store profile image
        $profilePath = null;

        if ($request->hasFile('profile')) {

            $file = $request->file('profile');

            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('assets/profiles');

            // create folder if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            $profilePath = 'assets/profiles/' . $filename;
        }

        $agencyId = Agency::where('agency_name', 'AGILE ONE')->value('id');
        $plainPassword = $request->password;

        // create user
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'product_id' => $request->product_id,
            'status'        => 1,
            'otp_enabled'   => 1,
            'city'          => $request->city,
            'state'         => $request->state,
            'zip'           => $request->zip,
            'address'       => $request->address,
            'agency_id'     => $agencyId,
            'date_of_birth' => $request->date_of_birth,
            'profile'       => $profilePath
        ]);

        // load relations (IMPORTANT for email)
        $user->load(['role', 'agency']);

        // send notification with password
        $user->notify(new UserCreatedNotification($user, $plainPassword));
        return response()->json([
            'success' => 'User has been created successfully.'
        ]);
    }
    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        $roleName = strtolower($authUser->role->name);
        $rules = [
            'name'          => 'required',
            'email'         => "required|email|unique:users,email,$id",
            'role_id'       => 'required',
            'date_of_birth' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],
            'city'          => 'required',
            'state'         => 'required',
            'zip'           => 'required',
            'address'       => 'required',
            'profile'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);

        $data = $request->except('_token', 'password', 'profile');
        $data['product_id'] = $request->product_id;
        $agencyId = Agency::where('agency_name', 'AGILE ONE')->value('id');

        $data['agency_id'] = $agencyId;
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile')) {

            // delete old file
            if (!empty($user->profile) && file_exists(public_path($user->profile))) {
                unlink(public_path($user->profile));
            }

            $file = $request->file('profile');

            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('assets/profiles');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            $data['profile'] = 'assets/profiles/' . $filename;
        }

        $user->update($data);

        return response()->json(['success' => 'User has been updated successfully.']);
    }
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Check if user is currently active and trying to be deactivated
        if ($user->status == true) {

            $hasOpenLeads = Lead::where('assigned_to', $user->id)
                ->whereNotIn('status', ['completed', 'lost'])
                ->exists();

            if ($hasOpenLeads) {
                return response()->json([
                    'success' => false,
                    'message' => 'This user still has active leads assigned. Please reassign them to another user before deactivating.'
                ], 400);
            }
        }

        // Toggle status
        $user->status = !$user->status;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => $user->status ? 'User activated.' : 'User deactivated.'
        ]);
    }
}
