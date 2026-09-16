<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Tambahan untuk akses tabel users
use Illuminate\Support\Facades\Hash; // Tambahan untuk enkripsi password
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // --- Jalur API (Postman / client JSON) ---
        // Route api tidak punya session, jadi pakai token Sanctum, bukan Auth::attempt + session
        if ($request->expectsJson()) {
            $user = User::where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah.',
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'data' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        // --- Jalur Web (form biasa, pakai session) ---
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan Role sesuai matriks Anda
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.katalog');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // --- TAMBAHAN FUNGSI REGISTRASI ---
    // Dipakai baik oleh route web (routes/web.php) maupun route api (routes/api.php)
    public function register(RegisterRequest $request)
    {
        // 1. Validasi otomatis dilakukan oleh RegisterRequest sebelum masuk ke sini

        // 2. Insert ke Database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'role' => 'peminjam', // Default saat daftar adalah peminjam
        ]);

        // 3. Beri response
        // Jika request datang dari Postman / client API (JSON), kembalikan JSON + token Sanctum
        if ($request->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil!',
                'data' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        }

        // Jika request datang dari Form Website asli, login-kan lalu redirect
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('peminjam.katalog')->with('success', 'Registrasi berhasil, selamat datang!');
    }
}