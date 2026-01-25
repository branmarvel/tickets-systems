@extends('adminlte::page')

@section('title', 'Detalles de la Actividad #' . $evento->id_Eventos)

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks"></i>&nbsp {{ $evento->id_Eventos }}.&nbsp Actividad del Ticket
                        #{{ $ticket_id }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> &nbsp Home</a></li>
                        <li class="breadcrumb-item active"><a
                                href="{{ route('eventos.index', ['ticket_id' => $ticket_id]) }}"><i
                                    class="fas fa-tasks"></i>&nbsp Actividades</a></li>
                        <li class="breadcrumb-item active"><i class="fas fa-tasks"></i>&nbsp #{{ $evento->id_Eventos }}
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
                            <h3 class="card-title"><i class="fas fa-tasks"></i>&nbsp Datos de la Actividad</h3>
                        </div>
                        <div class="card-body">
                            <p><strong><i class="fas fa-user"></i> &nbsp Usuario:</strong>
                                {{ $evento->usuarioRazonSocial->nombreCompleto }}
                            </p>
                            <p><strong><i class="fas fa-tag"></i> &nbsp Tipo de Actividad:</strong> &nbsp <i
                                    class="fa {{ $evento->tipoEventoClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                &nbsp {{ $evento->tipoEventoClasificacion->titulo }}</p>
                            <p><strong><i class="fas fa-align-left"></i> &nbsp Descripción:</strong>
                                {{ $evento->descripcion }}
                            </p>
                            <p><strong><i class="fas fa-file-alt"></i> &nbsp Archivo:</strong>
                                @if ($evento->archivo)
                                    <a href="#" onclick="openFileModal('{{ asset('storage/' . $evento->archivo) }}')"
                                        class="btn btn-primary btn-sm" data-toggle="modal">
                                        <i class="fas fa-eye"></i> Ver Archivo
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                            <p><strong><i class="fas fa-calendar-day"></i> &nbsp Fecha de la Actividad:</strong>
                                {{ $evento->fechaEvento ? \Illuminate\Support\Carbon::parse($evento->fechaEvento)->format('Y-m-d H:i:s') : 'N/A' }}
                            </p>
                            <p><strong><i class="fas fa-clock"></i> &nbsp Fecha de Inicio:</strong>
                                {{ $evento->fechaInicio ? \Illuminate\Support\Carbon::parse($evento->fechaInicio)->format('Y-m-d H:i:s') : 'N/A' }}
                            </p>
                            <p><strong><i class="fas fa-clock"></i> &nbsp Fecha de Fin:</strong>
                                {{ $evento->fechaFin ? \Illuminate\Support\Carbon::parse($evento->fechaFin)->format('Y-m-d H:i:s') : 'N/A' }}
                            </p>
                            <p><strong><i class="fas fa-coins"></i> &nbsp Costo (VES):</strong> {{ $evento->costo }}</p>
                            <p><strong><i class="fas fa-dollar-sign"></i> &nbsp Costo (USD):</strong> {{ $usdValue }}
                            </p>
                            <p><strong><i class="fas fa-ticket-alt"></i> &nbsp Ticket:</strong> {{ $evento->ticket_id }}
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('eventos.index', ['ticket_id' => $evento->ticket_id]) }}"
                                class="btn btn-secondary"><i class="fas fa-hand-point-left"></i> &nbsp Volver a la lista</a>
                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-primary">Editar &nbsp <i
                                        class="fas fa-edit"></i></a>
                            @endif
                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                                <form id="frmDatos" action="{{ route('eventos.destroy', $evento) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar &nbsp <i
                                            class="fas fa-trash-alt"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalViewFile" tabindex="-1" role="dialog" aria-labelledby="modalViewFileLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalViewFileLabel">Visualización de Archivo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <iframe id="fileFrame" src="" width="100%" height="500px" frameborder="0"></iframe>
                        </div>
                        <div class="modal-footer">
                            <a id="downloadLink" href="#" class="btn btn-secondary" download><i
                                    class="fas fa-download"></i> &nbsp Descargar</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        function openFileModal(fileUrl) {
            const modal = $('#modalViewFile');
            const fileFrame = modal.find('#fileFrame');
            const downloadLink = modal.find('#downloadLink');

            fileFrame.attr('src', fileUrl);
            downloadLink.attr('href', fileUrl);

            modal.modal('show');
        }

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
@endsection
