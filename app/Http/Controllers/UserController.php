<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Notifications\UserCreatedNotification;
use Chatify\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $request->merge([
            'start' => $request->start ?? 0,
            'length' => $request->length ?? 10,
        ]);
        $authUser = Auth::user();

        // Include the logged-in user's own record too - it was
        // previously excluded, so an Admin couldn't see themselves
        // in their own Users list. The agency/role scoping below
        // still applies to them like any other row.
        $query = User::with(['role', 'agency'])
            ->latest();
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($authUser->isMis() || $authUser->isAdmin()) {
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

            // Column sorting - maps the DataTables column index (sent
            // as order[0][column]/order[0][dir]) to an actual column,
            // same pattern used in LeadController@index and
            // RoleController@index. Role is sorted by the underlying
            // role_id (not the joined role name), same way Leads
            // sorts its Product column by product_id.
            $columns = [
                0 => 'name',
                1 => 'email',
                2 => 'role_id',
                3 => 'address',
                4 => 'otp_enabled',
            ];

            if ($request->has('order')) {

                $orderColumnIndex = $request->order[0]['column'] ?? 0;
                $orderDirection = $request->order[0]['dir'] ?? 'desc';

                if (isset($columns[$orderColumnIndex])) {
                    $query->reorder($columns[$orderColumnIndex], $orderDirection);
                }
            }

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
        $rules = [
            'name'          => 'required',
            // Soft-deleted users keep their row (deleted_at set), so
            // uniqueness only applies among active users - a
            // deleted user's email is free to reuse.
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
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
            'is_mobile' => ['required', 'boolean'],
            'is_tablet' => ['required', 'boolean'],
            'profile'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

            // store profile image
        $profilePath = null;
        $filename = null;

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('assets/profiles');
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
            'is_mobile' => $request->boolean('is_mobile'),
            'is_tablet' => $request->boolean('is_tablet'),
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
        $rules = [
            'name'          => 'required',
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'role_id'       => 'required',
            'product_id'    => ['required', 'array', 'min:1'],
            'product_id.*'  => ['exists:products,id'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
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

        $newFilename = null;

        if ($request->hasFile('profile')) {

            // delete old file from your own storage
            if (!empty($user->profile) && file_exists(public_path($user->profile))) {
                unlink(public_path($user->profile));
            }

            // delete old Chatify avatar file too, using the OLD filename
            // (derived from $user->profile, before we overwrite it)
            if (!empty($user->profile)) {
                $oldFilename = basename($user->profile); // e.g. "169..._download.jpg"
                $oldChatifyFile = storage_path('app/public/users-avatar/' . $oldFilename);
                if (file_exists($oldChatifyFile)) {
                    unlink($oldChatifyFile);
                }
            }

            $file = $request->file('profile');
            $newFilename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('assets/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $newFilename);

            $data['profile'] = 'assets/profiles/' . $newFilename;
        }

        $user->update($data);

        // sync to Chatify AFTER $user is updated
        if ($newFilename) {
            $chatifyDir = storage_path('app/public/users-avatar');
            if (!file_exists($chatifyDir)) {
                mkdir($chatifyDir, 0777, true);
            }

            $sourceFile = public_path($data['profile']);
            $destFile   = $chatifyDir . '/' . $newFilename;

            if (file_exists($sourceFile)) {
                copy($sourceFile, $destFile);
            }

            \Chatify\Models\UserSetting::updateOrCreate(
                ['user_id' => $user->id],
                ['avatar' => $newFilename]
            );
        }

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
        // Toggle status
        $user->status = !$user->status;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => $user->status ? 'User activated.' : 'User deactivated.'
        ]);
    }
    public function toggleOtp($id)
    {
        $authUser = Auth::user();

        // Only Admin and Super Admin can change OTP settings
        if (!$authUser->isAdminOrAbove()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to change OTP settings.'
            ], 403);
        }

        $user = User::findOrFail($id);

        // Toggle OTP status
        $user->otp_enabled = !$user->otp_enabled;
        $user->save();

        return response()->json([
            'success' => true,
            'otp_enabled' => (bool) $user->otp_enabled,
            'message' => $user->otp_enabled
                ? 'OTP login has been enabled for this user.'
                : 'OTP login has been disabled for this user.'
        ]);
    }
    public function toggleMobile($id)
    {
        $authUser = Auth::user();

        // Only Super Admin and Admin can change mobile login access
        if (!$authUser->isAdminOrAbove()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to change mobile login access.'
            ], 403);
        }

        $user = User::findOrFail($id);

        $user->is_mobile = !$user->is_mobile;
        $user->save();

        return response()->json([
            'success' => true,
            'is_mobile' => (bool) $user->is_mobile,
            'message' => $user->is_mobile
                ? 'Mobile login has been enabled for this user.'
                : 'Mobile login has been disabled for this user.'
        ]);
    }
    public function toggleTablet($id)
    {
        $authUser = Auth::user();

        // Only Super Admin and Admin can change tablet access
        if (!$authUser->isAdminOrAbove()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to change tablet login access.'
            ], 403);
        }

        $user = User::findOrFail($id);

        $user->is_tablet = !$user->is_tablet;
        $user->save();

        return response()->json([
            'success' => true,
            'is_tablet' => (bool) $user->is_tablet,
            'message' => $user->is_tablet
                ? 'Tablet login has been enabled for this user.'
                : 'Tablet login has been disabled for this user.'
        ]);
    }
}
