@extends('adminlte::page')

@section('title', $clasificacion->titulo)

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fa {{ $clasificacion->icono ?? 'fa-question-circle' }}"></i> &nbsp
                        {{ $clasificacion->titulo }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fa fa-home"></i> &nbsp Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('clasificaciones.index') }}"><i
                                    class="fa fa-star"></i> &nbsp Clasificaciones</a></li>
                        <li class="breadcrumb-item active"><i
                                class="fa {{ $clasificacion->icono ?? 'fa-question-circle' }}"></i> &nbsp
                            {{ $clasificacion->titulo }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clipboard-list"></i>&nbsp Lista de
                                {{ $clasificacion->titulo }}</h3>
                            <a href="{{ route('subclasificaciones.create', $clasificacion->id_Clasificacion) }}"
                                class="btn btn-primary float-right">Crear {{ $clasificacion->titulo }} &nbsp<i
                                    class="fa {{ $clasificacion->icono ?? 'fa-question-circle' }}"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="subclasificaciones-table" class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i>&nbsp ID</th>
                                            <th><i class="fas fa-heading"></i>&nbsp Título</th>
                                            <th><i class="fas fa-align-left"></i>&nbsp Descripción</th>
                                            <th><i class="fas fa-icons"></i>&nbsp Icono</th>
                                            <th><i class="fas fa-cogs"></i>&nbsp Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subclasificaciones as $subclasificacion)
                                            <tr>
                                                <td>{{ $subclasificacion->id_Clasificacion }}</td>
                                                <td>{{ $subclasificacion->titulo }}</td>
                                                <td>{{ $subclasificacion->descripcion }}</td>
                                                <td><i class="fa {{ $subclasificacion->icono }}"></i></td>
                                                <td>
                                                    <a href="{{ route('subclasificaciones.edit', [$clasificacion->id_Clasificacion, $subclasificacion->id_Clasificacion]) }}"
                                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <form id="frmDatos"
                                                        action="{{ route('subclasificaciones.destroy', [$clasificacion->id_Clasificacion, $subclasificacion->id_Clasificacion]) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                                class="fas fa-trash-alt"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('clasificaciones.index') }}" class="btn btn-secondary"><i
                                    class="fas fa-hand-point-left"></i>&nbsp Volver al listado</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar DataTable si no ha sido inicializada
            if (!$.fn.DataTable.isDataTable('#subclasificaciones-table')) {
                $('#subclasificaciones-table').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/2.1.4/i18n/es-ES.json',
                    },
                    dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>' +
                        'rt' +
                        '<"row"<"col-sm-6"i><"col-sm-6"p>>',
                    buttons: [{
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i>&nbsp ',
                            titleAttr: 'Exportar a Excel',
                            className: 'btn btn-success',
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i>&nbsp ',
                            titleAttr: 'Exportar a PDF',
                            className: 'btn btn-danger',
                            orientation: 'landscape',
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i>&nbsp ',
                            titleAttr: 'Imprimir',
                            className: 'btn btn-info',
                        },
                        
                    ],
                    columnDefs: [{
                        targets: [-1, -2],
                        orderable: false // Desactiva el ordenamiento
                    }]
                });
            }
            @if (session('agregado'))
                Swal.fire({
                    title: 'Agregado',
                    text: "{{ session('agregado') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('actualizar'))
                Swal.fire({
                    title: 'Actualizado',
                    text: "{{ session('actualizar') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    title: 'Eliminado',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            // SweetAlert2 para confirmación de eliminación
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

@section('css')
@stop

@endsection
