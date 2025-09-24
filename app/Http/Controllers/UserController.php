<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
    // Base query: include all users (so admins also appear in the list)
    $query = User::with('assets');
    // Optional role filter (role=admin or role=user)
    if (request()->filled('role') && in_array(request('role'), ['user','admin','super-admin'])) {
        $query->where('role', request('role'));
    }
        // Apply search filter
        if(request()->filled('search')) {
            $search = request()->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        // Filter by asset tipe if provided
        if (request()->filled('tipe')) {
            $tipeFilter = request()->input('tipe');
            $query->whereHas('assets', function($q) use ($tipeFilter) {
                $q->where('tipe', $tipeFilter);
            });
        }
        // Filter by asset jenis_aset if provided
        if (request()->filled('jenis_aset')) {
            $jenisFilter = request()->input('jenis_aset');
            $query->whereHas('assets', function($q) use ($jenisFilter) {
                $q->where('jenis_aset', $jenisFilter);
            });
        }
        // Filter by user lokasi if provided
        if (request()->filled('lokasi')) {
            $lokasiFilter = request()->input('lokasi');
            $query->where('lokasi', $lokasiFilter);
        }
    $users = $query->paginate(10)->appends(request()->only('search', 'tipe', 'jenis_aset', 'lokasi'));
        // Get distinct asset types for filter dropdown
    $tipes = \App\Models\Asset::select('tipe')->distinct()->pluck('tipe');
    $jenisAsets = \App\Models\Asset::select('jenis_aset')->distinct()->pluck('jenis_aset');
    // Get distinct user locations for filter dropdown
    $lokasis = User::select('lokasi')->distinct()->pluck('lokasi');
    return view('users.index', compact('users', 'tipes', 'jenisAsets', 'lokasis'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nik' => 'nullable|string|max:100|unique:users,nik',
            'avatar' => 'nullable|image|max:2048',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:user,admin',
            'lokasi' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'project' => $request->project,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user', // Default role untuk PIC
            'lokasi' => $request->lokasi,
            'jabatan' => $request->jabatan,
        ];
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }
        $user = User::create($data);

        return redirect()->route('users.index')->with('success', 'User PIC berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nik' => 'nullable|string|max:100|unique:users,nik,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'lokasi' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
        ]);

        $data = $request->only('name', 'email', 'nik', 'role', 'lokasi', 'project', 'jabatan');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
