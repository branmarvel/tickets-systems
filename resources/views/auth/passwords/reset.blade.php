@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', 'Restablecer Contraseña')

@section('auth_header', 'Ingrese su nueva contraseña')

@section('auth_body')
<form action="{{ route('password.update') }}" method="post">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <!-- Email oculto (se obtiene del enlace) -->
    <input type="hidden" name="email" value="{{ request()->email }}">

    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nueva Contraseña" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
        @error('password')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <div class="input-group mb-3">
        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar Contraseña" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Restablecer Contraseña</button>
</form>
@endsection
