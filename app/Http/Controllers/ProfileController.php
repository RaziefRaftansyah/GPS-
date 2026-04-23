<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->boolean('remove_avatar')) {
            $this->deleteManagedProfilePhoto($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('avatar_file')) {
            $storedPath = $request->file('avatar_file')->store('avatars', 'public');
            $this->deleteManagedProfilePhoto($user->profile_photo_path);
            $user->profile_photo_path = $storedPath;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function deactivate(Request $request): RedirectResponse
    {
        $request->validateWithBag('accountDeactivation', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        $user->forceFill([
            'is_active' => false,
        ])->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'account-deactivated');
    }

    private function deleteManagedProfilePhoto(?string $profilePhotoPath): void
    {
        if (blank($profilePhotoPath)) {
            return;
        }

        $normalizedPath = ltrim((string) $profilePhotoPath, '/');

        if (Str::startsWith($normalizedPath, 'storage/avatars/')) {
            $normalizedPath = Str::after($normalizedPath, 'storage/');
        }

        if (! Str::startsWith($normalizedPath, 'avatars/')) {
            return;
        }

        Storage::disk('public')->delete($normalizedPath);
    }
}
