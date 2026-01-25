<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\RazonSocial;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
{
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    public function reset(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:RazonSocial,email',
        'password' => 'required|confirmed|min:8',
    ], [
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'email.exists' => 'El correo no existe en nuestros registros.'
    ]);

    $user = RazonSocial::where('email', $request->email)->first();
    
    if ($user) {
        // Encripta la contraseña antes de guardarla
        $user->contraseña = Hash::make($request->password);
        $user->save();

        // Elimina el token para que no pueda usarse nuevamente
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Contraseña restablecida con éxito.');
    }

    return back()->withErrors(['email' => 'El correo no existe en nuestros registros.']);
}

}

