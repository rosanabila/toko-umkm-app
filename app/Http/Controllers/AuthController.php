<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Admin!');
            } elseif ($user->isPenjual()) {
                return redirect()->route('seller.dashboard')->with('success', 'Selamat datang kembali ke toko Anda!');
            }
            
            return redirect()->intended('/')->with('success', 'Berhasil masuk!');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:pembeli,penjual', // Allow registering as buyer or seller
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        Auth::login($user);

        if ($user->isPenjual()) {
            // Automatically create a dummy store profile that they must complete later
            $user->store()->create([
                'name' => 'Toko ' . $user->name,
                'slug' => 'toko-' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $user->name)),
                'description' => 'Profil toko baru Anda. Edit profil untuk mengubah deskripsi.',
            ]);
            return redirect()->route('seller.dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di dashboard penjual Anda.');
        }

        return redirect('/')->with('success', 'Pendaftaran akun berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah keluar dari aplikasi.');
    }
}
