@extends('adminlte::page')

@section('title', 'Detalles de ' . $razonSocial->nombreCompleto)

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-list"></i>&nbsp Detalles de {{ $razonSocial->nombreCompleto }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fa fa-home"></i>&nbsp Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('razon_social.index') }}"><i
                                    class="fa fa-user-plus"></i>&nbsp Razones Sociales</a></li>
                        <li class="breadcrumb-item active"><i class="fa fa-user"></i>&nbsp
                            {{ $razonSocial->nombreCompleto }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-plus"></i>&nbsp Entidad:</h3>
                        </div>
                        <div class="card-body">
                            <p><strong><i class="fas fa-user"></i>&nbsp Nombre Completo:</strong>
                                {{ $razonSocial->nombreCompleto }}</p>
                            <p><strong><i class="fas fa-id-badge"></i>&nbsp Tipo de Persona:</strong>&nbsp<i
                                    class="fa {{ $razonSocial->tipoPersonaClasificacion->icono ?? 'fa-question-circle' }}"></i>&nbsp{{ $razonSocial->tipoPersonaClasificacion->titulo ?? 'N/A' }}
                            </p>
                            <p><strong><i class="fas fa-id-card"></i>&nbsp Número de Identificación:</strong>
                                {{ $razonSocial->nroIdentificacion }}</p>
                            <p><strong><i class="fas fa-phone"></i>&nbsp Teléfono:</strong> {{ $razonSocial->telefono }}</p>
                            <p><strong><i class="fas fa-envelope"></i>&nbsp Email:</strong> {{ $razonSocial->email }}</p>
                            <p><strong><i class="fas fa-map-marker-alt"></i>&nbsp Dirección:</strong>
                                {{ $razonSocial->direccion }}</p>
                            <p><strong><i class="fas fa-user-tag"></i>&nbsp Rol:</strong>&nbsp<i
                                    class="fa {{ $razonSocial->rolClasificacion->icono ?? 'fa-question-circle' }}"></i>&nbsp{{ $razonSocial->rolClasificacion->titulo ?? 'N/A' }}
                            </p>
                            <p><strong><i class="fas fa-key"></i>&nbsp Contraseña:</strong> {{ $razonSocial->contraseña }}
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('razon_social.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-hand-point-left"></i>&nbsp Volver a la lista</a>
                            <a href="{{ route('razon_social.edit', $razonSocial->id_RS) }}"
                                class="btn btn-primary">Editar&nbsp<i class="fas fa-edit"></i></a>
                            <form id="frmDatos" action="{{ route('razon_social.destroy', $razonSocial->id_RS) }}"
                                method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar&nbsp<i
                                        class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            $('form#frmDatos').on('submit', function(e) {
                e.preventDefault(); // Detiene el submit del formulario

                let form = this; // Guarda una referencia al formulario actual
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Si se confirma, envía el formulario
                    }
                });
            });
        });
    </script>
@stop
