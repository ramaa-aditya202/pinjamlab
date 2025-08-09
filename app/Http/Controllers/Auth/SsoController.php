<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SsoController extends Controller
{
    /**
     * Redirect ke SSO OAuth2 provider
     */
    public function redirect()
    {
        return Socialite::driver('sso')->redirect();
    }

    /**
     * Handle callback dari SSO OAuth2 provider
     */
    public function callback()
    {
        try {
            $ssoUser = Socialite::driver('sso')->user();
            
            // Cari user berdasarkan email
            $user = User::where('email', $ssoUser->getEmail())->first();
            
            if ($user) {
                // User sudah ada, login
                Auth::login($user);
            } else {
                // User belum ada, buat user baru
                $user = User::create([
                    'name' => $ssoUser->getName(),
                    'email' => $ssoUser->getEmail(),
                    'password' => bcrypt(str()->random(16)), // Random password karena login via SSO
                    'role' => $this->determineUserRole($ssoUser->getEmail()), // Tentukan role berdasarkan email/data
                    'email_verified_at' => now(),
                ]);
                
                Auth::login($user);
            }
            
            // Redirect berdasarkan role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('guru.dashboard');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan SSO: ' . $e->getMessage());
        }
    }

    /**
     * Tentukan role user berdasarkan email atau data dari SSO
     */
    private function determineUserRole($email)
    {
        // Contoh: jika email mengandung 'admin', maka role admin
        if (strpos($email, 'admin') !== false) {
            return 'admin';
        }
        
        // Default role adalah guru
        return 'guru';
    }
}
