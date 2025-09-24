<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.manage', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        // Handle avatar upload
        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus JPG atau PNG.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);
        $user = $request->user();

        // Safety net: ensure 'avatar' column exists (useful for SQLite/dev when migration wasn't applied)
        $canSaveAvatar = true;
        try {
            if (!Schema::hasColumn('users', 'avatar')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('avatar')->nullable();
                });
            }
        } catch (\Throwable $e) {
            $canSaveAvatar = false;
        }

        if ($request->hasFile('avatar') && $canSaveAvatar) {
            // Delete old avatar if exists
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }
        $user->save();
        return Redirect::route('profile.edit')->with('success', $canSaveAvatar ? 'Foto profil berhasil diperbarui.' : 'Profil tersimpan, namun kolom avatar belum ada di DB. Jalankan: php artisan migrate');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'current_password.current_password' => 'Password saat ini tidak sesuai',
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('profile.edit')->with('success', 'Password berhasil diubah!');
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
