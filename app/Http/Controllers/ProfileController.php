<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Lead;
use App\Models\User;
class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */

    public function index()
    {
        $user = Auth::user();

        if ($user->role && strtolower($user->role->name) === 'super admin') {

            $leadCount = Lead::count();
            $teamCount = User::count();

        } else {

            $leadCount = Lead::where('agency_id', $user->agency_id)->count();

            $teamCount = User::where('agency_id', $user->agency_id)
                ->where('status', 1)
                ->where('id', '!=', $user->id)
                ->whereHas('role', function ($q) {
                    $q->whereIn('name', ['MIS', 'Account Executive']);
                })
                ->count();
        }

        return view('profile.index', compact('leadCount', 'teamCount'));
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'date_of_birth' => 'required|date',
            'city'          => 'required',
            'state'         => 'required',
            'zip'           => 'required',
            'address'       => 'required',
            'profile'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.index')
                ->withErrors($validator)
                ->withInput();
        }

        // Handle profile photo upload
        if ($request->hasFile('profile')) {
            // Delete old photo from storage if it exists
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }
            $profilePath = $request->file('profile')->store('profiles', 'public');
            $user->profile = $profilePath;
        }
        if ($request->hasFile('profile')) {

            // delete old file (if exists)
            if ($user->profile && file_exists(public_path($user->profile))) {
                unlink(public_path($user->profile));
            }

            $file = $request->file('profile');

            $filename = time() . '_' . $file->getClientOriginalName();

            $destinationPath = public_path('assets/profiles');

            // create folder if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            // store relative path in DB
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

        return back()
        ->withErrors($validator)
        ->withInput()->with('success', 'Profile updated successfully.');
    }
}
