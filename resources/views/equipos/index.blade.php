@extends('adminlte::page')

@section('title', 'Equipos')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fa fa-boxes"></i> &nbspEquipos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                        <li class="breadcrumb-item active"><i class="fas fa-boxes"></i>&nbsp Equipos</li>
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
                            <h3 class="card-title"><i class="fas fa-clipboard-list"></i>&nbsp Listado de Equipos</h3>
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="{{ route('equipos.create') }}" class="btn btn-primary">Agregar &nbsp<i
                                        class="fa fa-boxes"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="equipos-table" class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i>&nbsp ID</th>
                                            <th><i class="fas fa-align-left"></i>&nbsp Descripción</th>
                                            <th><i class="fas fa-tag"></i>&nbsp Marca</th>
                                            <th><i class="fas fa-cogs"></i>&nbsp Modelo</th>
                                            <th><i class="fas fa-barcode"></i>&nbsp Nro de Serie</th>
                                            <th><i class="fas fa-stream"></i>&nbsp Línea</th>
                                            <th><i class="fas fa-cube"></i>&nbsp Equipo</th>
                                            <th><i class="fas fa-coins"></i>&nbsp Precio (REF)</th>
                                            <th><i class="fas fa-info-circle"></i>&nbsp Motivo</th>
                                            <th><i class="fas fa-user"></i>&nbsp Dueño</th>
                                            <th><i class="fas fa-cogs"></i>&nbsp Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($equipos as $equipo)
                                            <tr>
                                                <td>{{ $equipo->id_Equipos }}</td>
                                                <td>{{ $equipo->descripcion }}</td>
                                                <td><i
                                                        class="fa {{ $equipo->marcaClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $equipo->marcaClasificacion->titulo ?? 'N/A' }}</td>
                                                <td>{{ $equipo->modelo }}</td>
                                                <td>{{ $equipo->nroSerie }}</td>
                                                <td><i
                                                        class="fa {{ $equipo->lineaClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $equipo->lineaClasificacion->titulo ?? 'N/A' }}</td>
                                                <td><i
                                                        class="fa {{ $equipo->tipoEquipoClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $equipo->tipoEquipoClasificacion->titulo ?? 'N/A' }}</td>
                                                <td>{{ $equipo->precio }}</td>
                                                <td><i
                                                        class="fa {{ $equipo->motivoClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $equipo->motivoClasificacion->titulo ?? 'N/A' }}</td>
                                                <td>{{ $equipo->dueñoRazonSocial->nombreCompleto ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('equipos.edit', $equipo->id_Equipos) }}"
                                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <form id="frmDatos"
                                                        action="{{ route('equipos.destroy', $equipo->id_Equipos) }}"
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
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Inicializar DataTable si no ha sido inicializada
            if (!$.fn.DataTable.isDataTable('#equipos-table')) {
                $('#equipos-table').DataTable({
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
                        targets: -1,
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
    <!-- Puedes incluir estilos personalizados aquí -->
@stop
