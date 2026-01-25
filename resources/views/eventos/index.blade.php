@extends('adminlte::page')

@section('title', 'Actividades')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks"></i> &nbsp
                        @if (isset($ticket))
                            Actividades del Ticket #{{ $ticket->id_Tickets }}
                        @else
                            Seleccione un Ticket para ver las Actividades
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> &nbsp Home</a></li>
                        <li class="breadcrumb-item active"><i class="fas fa-tasks"></i> &nbsp Actividades</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @if (!isset($ticket_id))
                                <form action="{{ route('eventos.index') }}" method="GET">
                                    <div class="form-group">
                                        <label for="ticket_id"><i class="fa fa-hand-point-down"></i> &nbsp Seleccionar
                                            Ticket</label>
                                        <select name="ticket_id" id="ticket_id" class="form-control">
                                            <option value="" disabled selected>Tickets</option>
                                            @foreach ($tickets as $ticket)
                                                <option value="{{ $ticket->id_Tickets }}">{{ $ticket->id_Tickets }} -
                                                    {{ $ticket->clienteRazonSocial->nombreCompleto }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-tasks"></i> &nbsp Ver
                                        Actividades</button>
                                </form>
                            @else
                                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> &nbsp Lista de Actividades para
                                    el Ticket ID: <strong>{{ $ticket->clienteRazonSocial->nombreCompleto }} -
                                        {{ $ticket_id }}</strong></h3>
                                <div class="card-tools">
                                    <a href="{{ route('eventos.index') }}" class="btn btn-secondary"><i
                                            class="fas fa-ticket-alt"></i> &nbsp Otro Ticket</a>
                                    <a href="{{ route('eventos.create', ['ticket_id' => $ticket_id]) }}"
                                        class="btn btn-primary">Agregar &nbsp <i class="fas fa-tasks"></i></a>
                                </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="eventos-table" class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> &nbsp ID</th>
                                            <th><i class="fas fa-user"></i> &nbsp Usuario</th>
                                            <th><i class="fas fa-tag"></i> &nbsp Tipo de Actividad</th>
                                            <th><i class="fas fa-align-left"></i> &nbsp Descripción</th>
                                            <th><i class="fas fa-file-alt"></i> &nbsp Archivo</th>
                                            <th><i class="fas fa-calendar-day"></i> &nbsp Fecha de la Actividad</th>
                                            <th><i class="fas fa-clock"></i> &nbsp Fecha de Inicio</th>
                                            <th><i class="fas fa-clock"></i> &nbsp Fecha de Fin</th>
                                            <th><i class="fas fa-coins"></i> &nbsp Costo (VES)</th>
                                            <th><i class="fas fa-cogs"></i> &nbsp Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($eventos as $evento)
                                            <tr>
                                                <td>{{ $evento->id_Eventos }}</td>
                                                <td>{{ $evento->usuarioRazonSocial->nombreCompleto ?? 'N/A' }}</td>
                                                <td><i
                                                        class="fa {{ $evento->tipoEventoClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $evento->tipoEventoClasificacion->titulo ?? 'N/A' }}</td>
                                                <td>{{ $evento->descripcion }}</td>
                                                <td>
                                                    @if ($evento->archivo)
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-toggle="modal" data-target="#fileModal"
                                                            data-file="{{ Storage::url($evento->archivo) }}">
                                                            <i class="fas fa-file-download"></i> &nbsp Ver archivo
                                                        </button>
                                                    @else
                                                        <i class="fas fa-times-circle"></i> &nbsp N/A
                                                    @endif
                                                </td>
                                                <td>{{ $evento->fechaEvento ? \Illuminate\Support\Carbon::parse($evento->fechaEvento)->format('Y-m-d H:i:s') : 'N/A' }}
                                                </td>
                                                <td>{{ $evento->fechaInicio ? \Illuminate\Support\Carbon::parse($evento->fechaInicio)->format('Y-m-d H:i:s') : 'N/A' }}
                                                </td>
                                                <td>{{ $evento->fechaFin ? \Illuminate\Support\Carbon::parse($evento->fechaFin)->format('Y-m-d H:i:s') : 'N/A' }}
                                                </td>
                                                <td>{{ $evento->costo }}</td>
                                                <td>
                                                    <a href="{{ route('eventos.show', $evento) }}"
                                                        class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                                    @if (!in_array(auth()->user()->rol, [10005, 10007]))
                                                        <a href="{{ route('eventos.edit', $evento) }}"
                                                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    @endif
                                                    @if (!in_array(auth()->user()->rol, [10005, 10007]))
                                                        <form id="frmDatos"
                                                            action="{{ route('eventos.destroy', $evento) }}" method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                                    class="fas fa-trash-alt"></i></button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="fileModalLabel">Visualización de Archivo</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="fileViewer" style="max-height: 600px; overflow-y: auto;">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a id="downloadLink" href="#" class="btn btn-secondary" download><i
                                                    class="fas fa-download"></i> &nbsp Descargar</a>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
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
            if (!$.fn.DataTable.isDataTable('#eventos-table')) {
                $('#eventos-table').DataTable({
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

            $('#fileModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var fileUrl = button.data('file');
                var modal = $(this);
                var fileExtension = fileUrl.split('.').pop().toLowerCase();
                var fileViewer = modal.find('#fileViewer');
                var downloadLink = modal.find('#downloadLink');

                fileViewer.html(''); // Limpia el contenido anterior
                downloadLink.attr('href', fileUrl); // Establece el enlace de descarga

                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    // Mostrar imagen
                    fileViewer.html('<img src="' + fileUrl +
                        '" class="img-fluid" alt="Imagen" style="max-width: 100%;">');
                } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                    // Mostrar video
                    fileViewer.html('<video controls style="width: 100%; height: auto;"><source src="' +
                        fileUrl +
                        '" type="video/' + fileExtension +
                        '">Tu navegador no soporta la visualización de este video.</video>');
                } else if (fileExtension === 'pdf') {
                    // Mostrar PDF
                    fileViewer.html('<embed src="' + fileUrl +
                        '" type="application/pdf" width="100%" height="600px">');
                } else {
                    // Si el archivo no puede ser visualizado, mostrar botón de descarga
                    fileViewer.html(
                        '<p>Este tipo de archivo no se puede visualizar. Por favor, descárgalo para verlo.</p>'
                        );
                }
            });
        });
    </script>
@stop

@section('css')
    <style>
        #fileViewer img,
        #fileViewer video {
            max-width: 100%;
            height: auto;
        }
    </style>
@stop
