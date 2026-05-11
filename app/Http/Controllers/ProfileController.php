<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_photo) {
            $oldPhotoPath = public_path('uploads/profile-photos/' . $user->profile_photo);
            if (File::exists($oldPhotoPath)) {
                File::delete($oldPhotoPath);
            }
        }

        $uploadPath = public_path('uploads/profile-photos');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $file = $request->file('profile_photo');
        $fileName = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);

        $user->profile_photo = $fileName;
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Foto de perfil actualizada correctamente.');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo) {
            $photoPath = public_path('uploads/profile-photos/' . $user->profile_photo);
            if (File::exists($photoPath)) {
                File::delete($photoPath);
            }

            $user->profile_photo = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('success', 'Foto de perfil eliminada.');
    }

    public function updateTheme(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['required', 'in:light,dark,auto'],
        ]);

        $user = $request->user();
        $user->theme = $validated['theme'] === 'auto' ? null : $validated['theme'];
        $user->save();

        return response()->json(['theme' => $user->theme]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
