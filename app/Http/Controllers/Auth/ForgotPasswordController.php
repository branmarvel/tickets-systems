<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\RazonSocial;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
{
    // Validar que el correo esté presente y sea un correo válido
    $request->validate([
        'email' => 'required|email',
    ]);

    // Verificar si el correo existe en la base de datos
    $user = RazonSocial::where('email', $request->email)->first();

    // Si no se encuentra el correo, devolver un error
    if (!$user) {
        return back()->withErrors(['email' => 'El correo no existe en nuestros registros.']);
    }

    // Verificar si el usuario ya ha solicitado un enlace en los últimos 60 segundos
    $cacheKey = 'password_reset_' . $request->email;
    if (Cache::has($cacheKey)) {
        return back()->withErrors(['email' => 'Debes esperar 60 segundos antes de solicitar otro enlace.']);
    }

    // Guardar en caché que el usuario ha solicitado un enlace
    Cache::put($cacheKey, true, 60); // 60 segundos

    // Intentar enviar el enlace de restablecimiento
    $status = Password::broker('users')->sendResetLink($request->only('email'));

    // Mostrar el mensaje de éxito o error en la vista
    if ($status === Password::RESET_LINK_SENT) {
        session()->flash('status', 'Se ha enviado un enlace para restablecer tu contraseña.');
    } else {
        session()->flash('error', 'Hubo un problema al enviar el enlace.');
    }

    return back();
}



}
