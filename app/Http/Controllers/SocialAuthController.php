<?php
// ID de clientes OAuth 2.0
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    // Redirige a Google para la autenticación
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google
    public function handleGoogleCallback()
    {
        try {
            // Obtener los datos del usuario desde Google
            $googleUser = Socialite::driver('google')->user();

            // Buscar si el usuario ya existe
            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                // Si el usuario no existe, crear uno nuevo
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(uniqid()), // Generar una contraseña temporal
                ]);
            }

            // Autenticar al usuario
            Auth::login($user, true);

            // Redirigir a la página deseada
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error de autenticación con Google');
        }
    }
    
}
