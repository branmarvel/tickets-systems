<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\RazonSocial;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nroIdentificacion' => ['required', 'string'],
            'contraseña' => ['required', 'string'],
        ]);

        $user = RazonSocial::where('nroIdentificacion', $credentials['nroIdentificacion'])->first();

        // Usamos Hash::check para comparar la contraseña ingresada con la encriptada
        if ($user && Hash::check($credentials['contraseña'], $user->contraseña)) {
            Auth::login($user);

            // Redirigir a tickets si el usuario es Cliente (rol 10007)
            if ($user->rol == 10007) {
                return redirect()->route('tickets.index');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'nroIdentificacion' => 'Número de identificación o contraseña no válidos.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}

// <?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use App\Models\RazonSocial;

// class AuthController extends Controller
// {
//     public function showLoginForm()
//     {
//         return view('auth.login');
//     }

//     public function login(Request $request)
// {
//     $credentials = $request->validate([
//         'nroIdentificacion' => ['required', 'string'],
//         'contraseña' => ['required', 'string'],
//     ]);

//     $user = RazonSocial::where('nroIdentificacion', $credentials['nroIdentificacion'])->first();

//     if ($user && $user->contraseña === $credentials['contraseña']) {
//         Auth::login($user);

//         // Redirigir a tickets si el usuario es Cliente (rol 10007)
//         if ($user->rol == 10007) {
//             return redirect()->route('tickets.index');
//         }

//         return redirect()->intended('dashboard');
//     }

//     return back()->withErrors([
//         'nroIdentificacion' => 'Número identificación no válido.',
//     ]);
// }


//     public function logout()
//     {
//         Auth::logout();
//         return redirect('/login');
//     }
// }
