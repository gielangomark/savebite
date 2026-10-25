<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Merchant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
        unset($validated['avatar_preset'], $validated['avatar_photo']);
        $user->fill($validated);
        $phoneChanged = $user->isDirty('phone');

        if ($request->hasFile('avatar_photo')) {
            if ($user->avatar && ! str_starts_with($user->avatar, 'preset:')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $request->file('avatar_photo')->store('profile-avatars', 'public');
        } elseif ($request->filled('avatar_preset')) {
            if ($user->avatar && ! str_starts_with($user->avatar, 'preset:')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = 'preset:' . $request->string('avatar_preset');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($phoneChanged && $user->isMerchant()) {
            Merchant::where('user_id', $user->id)
                ->update(['whatsapp' => $request->user()->phone]);
        }

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
}
