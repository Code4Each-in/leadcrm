<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Lead;
use App\Models\Product;
use App\Models\User;

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

        if ($request->hasFile('profile')) {
            $this->replaceProfilePhoto($user, $request->file('profile'));
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

    /**
     * AJAX - the avatar's camera button. Saves just the photo, the
     * moment it is picked: no Save Changes, and none of the other
     * profile fields are needed (or touched).
     */
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'profile.required' => 'Please choose a photo.',
            'profile.image'    => 'The file must be an image.',
            'profile.mimes'    => 'The photo must be a JPG, JPEG or PNG.',
            'profile.max'      => 'The photo may not be larger than 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $this->replaceProfilePhoto($user, $request->file('profile'));

        return response()->json([
            'success'     => 'Profile photo updated.',
            'profile_url' => asset($user->profile),
        ]);
    }

    /**
     * Stores $file as the user's profile photo and removes the old one.
     * Same convention as UserController@store/@update (public_path
     * assets/profiles), plus the copy Chatify shows as the chat avatar.
     */
    private function replaceProfilePhoto(User $user, UploadedFile $file): void
    {
        if ($user->profile && file_exists(public_path($user->profile))) {
            unlink(public_path($user->profile));
        }

        if (!empty($user->profile)) {
            $oldChatifyFile = storage_path('app/public/users-avatar/' . basename($user->profile));

            if (file_exists($oldChatifyFile)) {
                unlink($oldChatifyFile);
            }
        }

        // A generated name - the original could contain spaces or
        // characters that break the URL.
        $filename = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
        $destinationPath = public_path('assets/profiles');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);

        $user->profile = 'assets/profiles/' . $filename;
        $user->save();

        $chatifyDir = storage_path('app/public/users-avatar');

        if (!file_exists($chatifyDir)) {
            mkdir($chatifyDir, 0777, true);
        }

        copy(public_path($user->profile), $chatifyDir . '/' . $filename);

        \Chatify\Models\UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $filename]
        );
    }
}
