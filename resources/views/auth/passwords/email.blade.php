@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', 'Restablecer Contraseña')

@section('auth_header', 'Restablecer Contraseña')

@section('auth_body')
<form id="resetPasswordForm" action="{{ route('password.email') }}" method="post">
    @csrf

    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Correo electrónico" value="{{ old('email') }}" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>

    <button type="submit" id="submitButton" class="btn btn-primary btn-block">Enviar enlace</button>
</form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Verificar si hay un mensaje de éxito o error en la sesión
            @if (session('status'))
                Swal.fire({
                    title: 'Enlace Enviado',
                    text: '{{ session('status') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @elseif(session('error'))
                Swal.fire({
                    title: 'Error',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
@stop
