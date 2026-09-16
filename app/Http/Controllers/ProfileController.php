<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Lead;
use App\Models\Product;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function index()
    {
        $user = Auth::user();

        // Dynamic, real stats instead of the old hardcoded "20"
        // Lead count - leads actually created by this user, and
        // their most recent login (from the same login_logs table
        // the Login Logs page reads).
        $leadCount = Lead::where('created_by', $user->id)->count();

        $lastLogin = $user->LoginLog()->latest('login_at')->first();

        $assignedProducts = Product::whereIn('id', $user->product_id ?? [])
            ->orderBy('name')
            ->get();

        return view('profile.index', compact('leadCount', 'lastLogin', 'assignedProducts'));
    }

    /**
     * AJAX update - name/email/date_of_birth/address/city/state/zip
     * and, optionally, the profile photo. Returns JSON (success
     * message + the fresh profile image URL) instead of redirecting,
     * so the page never reloads and the header avatar can be synced
     * from the response.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'date_of_birth' => 'required|date|before_or_equal:today',
            'city'          => 'required',
            'state'         => 'required',
            'zip'           => 'required',
            'address'       => 'required',
            'profile'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Profile photo - same storage convention (public_path
        // assets/profiles) used by UserController@store/@update, so
        // an admin-created and a self-updated photo resolve the same
        // way everywhere they're displayed.
        if ($request->hasFile('profile')) {

            if ($user->profile && file_exists(public_path($user->profile))) {
                unlink(public_path($user->profile));
            }

            $file = $request->file('profile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('assets/profiles');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            $user->profile = 'assets/profiles/' . $filename;
        }

        $user->name          = $request->name;
        $user->email         = $request->email;
        $user->date_of_birth = $request->date_of_birth;
        $user->city          = $request->city;
        $user->state         = $request->state;
        $user->zip           = $request->zip;
        $user->address       = $request->address;
        $user->save();

        return response()->json([
            'success'      => 'Profile updated successfully.',
            'profile_url'  => $user->profile ? asset($user->profile) : asset('assets/images/default-profile.png'),
            'name'         => $user->name,
            'email'        => $user->email,
        ]);
    }
}
