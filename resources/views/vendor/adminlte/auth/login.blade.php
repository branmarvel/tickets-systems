@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', 'Login')

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
@php( $login_url = $login_url ? route($login_url) : '' )
@php( $register_url = $register_url ? route($register_url) : '' )
@php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
@php( $login_url = $login_url ? url($login_url) : '' )
@php( $register_url = $register_url ? url($register_url) : '' )
@php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', 'Iniciar sesión')

@section('auth_body')
<form action="{{ $login_url }}" method="post">
    @csrf

    {{-- Campo de Número de Identificación --}}
    <div class="input-group mb-3">
        <input type="text" name="nroIdentificacion" id="nroIdentificacion" class="form-control @error('nroIdentificacion') is-invalid @enderror" value="{{ old('nroIdentificacion') }}" placeholder="Número de Identificación" autofocus oninput="validateNumberInput(event)">
        <script>
            function validateNumberInput(event) {
                const input = event.target;
                // Solo permitir números (0-9)
                input.value = input.value.replace(/[^0-9]/g, '');
            }
        </script>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-id-card {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>

        @error('nroIdentificacion')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    {{-- Campo de Contraseña --}}
    <div class="input-group mb-3">
        <input type="password" name="contraseña" class="form-control @error('contraseña') is-invalid @enderror" placeholder="Contraseña">

        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>
        </div>

        @error('contraseña')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    {{-- Botón de inicio de sesión --}}
    <div class="row">
        <div class="col-7">
            <div class="icheck-primary" title="Recordar sesión">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                <label for="remember">
                    Recordarme
                </label>
            </div>
        </div>

        <div class="col-5">
            <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                <span class="fas fa-sign-in-alt"></span>
                Acceder
            </button>
        </div>
    </div>

</form>
@stop


@section('auth_footer')

@if($password_reset_url)
    <p class="my-0">
        <a href="{{ $password_reset_url }}">
            Olvidé mi contraseña
        </a>
    </p>
@endif



<!-- @if($register_url)
        <p class="my-0">
            <a href="#">
                Registrar una nueva cuenta
            </a>
        </p>
    @endif -->
@stop


{{-- @section('auth_footer')

    <!-- @if($password_reset_url)
        <p class="my-0">
            <a href="{{ $password_reset_url }}">
Olvidé mi contraseña
</a>
</p>
@endif -->


<!-- @if($register_url)
        <p class="my-0">
            <a href="{{ $register_url }}">
                Registrar una nueva cuenta
            </a>
        </p>
    @endif -->
@stop --}}