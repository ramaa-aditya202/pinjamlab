<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OAuth2Controller extends Controller
{
    /**
     * Redirect ke OAuth2 provider
     */
    public function redirect()
    {
        $config = config('services.oauth2');
        
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect'],
            'response_type' => 'code',
            'scope' => 'read:user',
            'state' => Str::random(40)
        ];
        
        session(['oauth2_state' => $params['state']]);
        
        $url = $config['authorize_url'] . '?' . http_build_query($params);
        
        return redirect($url);
    }

    /**
     * Handle callback dari OAuth2 provider
     */
    public function callback(Request $request)
    {
        try {
            // Verifikasi state
            if (!$request->has('state') || $request->state !== session('oauth2_state')) {
                return redirect()->route('login')->with('error', 'Invalid state parameter');
            }

            if ($request->has('error')) {
                return redirect()->route('login')->with('error', 'OAuth2 Error: ' . $request->error);
            }

            if (!$request->has('code')) {
                return redirect()->route('login')->with('error', 'Authorization code not received');
            }

            $config = config('services.oauth2');
            
            // Exchange code for access token
            $tokenResponse = Http::post($config['token_url'], [
                'grant_type' => 'authorization_code',
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect_uri' => $config['redirect'],
                'code' => $request->code,
            ]);

            if (!$tokenResponse->successful()) {
                return redirect()->route('login')->with('error', 'Failed to get access token');
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                return redirect()->route('login')->with('error', 'No access token received');
            }

            // Get user info
            $userResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($config['user_url']);

            if (!$userResponse->successful()) {
                return redirect()->route('login')->with('error', 'Failed to get user information');
            }

            $userData = $userResponse->json();
            
            if (!isset($userData['email']) || !isset($userData['name'])) {
                return redirect()->route('login')->with('error', 'Incomplete user data received');
            }

            // Find or create user
            $user = User::where('email', $userData['email'])->first();
            
            if ($user) {
                // Update user data if needed
                $user->update(['name' => $userData['name']]);
                Auth::login($user);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt(Str::random(16)), // Random password for OAuth users
                    'role' => $this->determineUserRole($userData['email']),
                    'email_verified_at' => now(),
                ]);
                
                Auth::login($user);
            }

            session()->forget('oauth2_state');

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('guru.dashboard');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'OAuth2 error: ' . $e->getMessage());
        }
    }

    /**
     * Determine user role based on email
     */
    private function determineUserRole($email)
    {
        // Check if email contains 'admin'
        if (strpos(strtolower($email), 'admin') !== false) {
            return 'admin';
        }
        
        // List of admin emails (you can customize this)
        $adminEmails = [
            'admin@maallathifahcikbar.sch.id',
            'kepala@maallathifahcikbar.sch.id',
            'wakil@maallathifahcikbar.sch.id'
        ];
        
        if (in_array(strtolower($email), array_map('strtolower', $adminEmails))) {
            return 'admin';
        }
        
        return 'guru';
    }
}