<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        // Debug logging untuk melacak redirect issue
        \Log::info('Login attempt', [
            'email' => $credentials['email'],
            'app_url' => config('app.url'),
            'request_url' => $request->url(),
            'intended_url' => session()->get('url.intended'),
        ]);

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Cek apakah user aktif
            if (!$user->is_active) {
                Auth::logout();
                \Log::warning('Inactive user login attempt', ['email' => $credentials['email']]);
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.'
                ])->withInput();
            }

            // Update last login
            $user->updateLastLogin();
            
            $request->session()->regenerate();
            
            $redirectUrl = route('dashboard');
            \Log::info('Login successful, redirecting to', ['url' => $redirectUrl, 'user_id' => $user->id]);
            
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang, ' . $user->display_name . '!');
        }

        \Log::warning('Login failed for email: ' . $credentials['email']);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        auth()->logout();

        // Biar token CSRF & session gak basi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    /**
     * Tampilkan halaman ganti password
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Proses ganti password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        // Cek password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini salah'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}