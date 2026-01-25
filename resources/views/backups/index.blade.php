@extends('adminlte::page')

@section('title', 'Copias de Seguridad')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fa fa-hdd"></i> &nbsp Copias de Seguridad</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fa fa-home"></i> &nbsp Home</a></li>
                        <li class="breadcrumb-item active"><i class="fa fa-hdd"></i> &nbsp Copias de Seguridad</li>
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
                            <h3 class="card-title"><i class="fas fa-database"></i>&nbsp Listado de Copias de Seguridad</h3>
                            <div class="card-tools">
                                <form action="{{ route('backups.create') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Agregar &nbsp <i
                                            class="fa fa-hdd"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="backups-table" class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><i class="fas fa-file-alt"></i>&nbsp Archivo</th>
                                            <th><i class="fas fa-calendar-alt"></i>&nbsp Fecha</th>
                                            <th><i class="fas fa-cogs"></i>&nbsp Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($backups as $backup)
                                            <tr>
                                                <td>{{ basename($backup) }}</td>
                                                <td>{{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($backup))->format('Y-m-d H:i:s') }}
                                                </td>
                                                <td>
                                                    <form action="{{ route('backups.restore') }}" method="POST"
                                                        class="restore-form" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="backup_file"
                                                            value="{{ basename($backup) }}">
                                                        <button type="submit" class="btn btn-warning btn-sm"><i
                                                                class="fas fa-undo-alt"></i></button>
                                                    </form>
                                                    <a href="{{ route('backups.download', basename($backup)) }}"
                                                        class="btn btn-success btn-sm"><i class="fas fa-download"></i></a>
                                                    <form action="{{ route('backups.delete', basename($backup)) }}"
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
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3">Restaurando la base de datos, por favor espera...</p>
                </div>
            </div>
        </div>
    </div>

@section('js')
    <script>
        $(document).ready(function() {
            $('#backups-table').DataTable({
                "responsive": true,
                "autoWidth": false,
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
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
            console.log('Página de Clasificaciones cargada.');
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
            $('.restore-form').on('submit', function() {
                $('#loadingModal').modal('show');
            });
        });
    </script>
@stop
@section('css')
@stop
@endsection
