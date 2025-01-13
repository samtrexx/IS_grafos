<?php
// ID de clientes OAuth 2.0
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    // Redirigir al usuario a Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Manejar la respuesta de Google
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
    
            $existingUser = User::where('email', $user->getEmail())->first();
    
            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'google_id' => $user->getId(),
                    'avatar' => $user->getAvatar(),
                    'password' => bcrypt('default_password'),
                ]);
    
                Auth::login($newUser);
            }
    
            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/')->withErrors('Error al autenticar con Google: ' . $e->getMessage());
        }
    }
    
}
