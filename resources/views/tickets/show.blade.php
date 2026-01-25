@extends('adminlte::page')

@section('title', 'Detalles del Ticket #' . $ticket->id_Tickets)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-clipboard-list"></i>&nbsp Detalles del Ticket #{{ $ticket->id_Tickets }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}"><i class="fas fa-ticket-alt"></i>&nbsp Tickets</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-ticket-alt"></i>&nbsp #{{ $ticket->id_Tickets }}</i>
                    </li>
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
                        <h3 class="card-title"><i class="fas fa-ticket-alt"></i>&nbsp Ticket:</h3>
                    </div>
                    <div class="card-body">
                        <p><i class="fas fa-user"></i>&nbsp <strong>Cliente:</strong>
                            {{ $ticket->clienteRazonSocial->nombreCompleto }}
                        </p>
                        <p><i class="fas fa-user-cog"></i>&nbsp <strong>Técnico:</strong>
                            {{ $ticket->tecnicoAsignadoRazonSocial->nombreCompleto }}
                        </p>
                        <p><i class="fas fa-box"></i>&nbsp <strong>Equipo:</strong>
                            {{ $ticket->propiedadEquipo->nroSerie . ' (' . $ticket->propiedadEquipo->tipoEquipoClasificacion->titulo . ' - ' . $ticket->propiedadEquipo->marcaClasificacion->titulo . ')' }}
                        </p>
                        <p><i class="fa {{ $ticket->statusClasificacion->icono ?? 'fa-question-circle' }}"></i>&nbsp
                            <strong>Estado:</strong> &nbsp {{ $ticket->statusClasificacion->titulo }}
                        </p>
                        <p><i class="fas fa-calendar-alt"></i>&nbsp <strong>Fecha de Solicitud:</strong>
                            {{ $ticket->fechaSolicitud ? \Illuminate\Support\Carbon::parse($ticket->fechaSolicitud)->format('Y-m-d H:i:s') : 'N/A' }}
                        </p>
                        <p><i class="fas fa-calendar-check"></i>&nbsp <strong>Fecha de Asignación:</strong>
                            {{ $ticket->fechaAsignacion ? \Illuminate\Support\Carbon::parse($ticket->fechaAsignacion)->format('Y-m-d H:i:s') : 'N/A' }}
                        </p>
                        <p><i class="fas fa-calendar-day"></i>&nbsp <strong>Fecha de Diagnóstico:</strong>
                            {{ $ticket->fechaDiagnostico ? \Illuminate\Support\Carbon::parse($ticket->fechaDiagnostico)->format('Y-m-d H:i:s') : 'N/A' }}
                        </p>
                        <p><i class="fas fa-calendar-check"></i>&nbsp <strong>Fecha de Aprobación:</strong>
                            {{ $ticket->fechaAprobacion ? \Illuminate\Support\Carbon::parse($ticket->fechaAprobacion)->format('Y-m-d H:i:s') : 'N/A' }}
                        </p>
                        <p><i class="fas fa-tools"></i>&nbsp <strong>Fecha de Reparación:</strong>
                            {{ $ticket->fechaReparacion ? \Illuminate\Support\Carbon::parse($ticket->fechaReparacion)->format('Y-m-d H:i:s') : 'N/A' }}
                        </p>
                        <p><i class="fas fa-calendar-check"></i>&nbsp <strong>Fecha de Finalización:</strong>
                            {{ $ticket->fechaFinalizacion ? \Illuminate\Support\Carbon::parse($ticket->fechaFinalizacion)->format('Y-m-d H:i:s') : 'N/A' }}
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i
                                class="fa fa-hand-point-left"></i>&nbsp Volver a la lista</a>
                        <a href="{{ route('eventos.index', ['ticket_id' => $ticket->id_Tickets]) }}"
                            class="btn btn-info"><i class="fas fa-tasks"></i>&nbsp Actividades</a>
                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                        <a href="{{ route('tickets.edit', $ticket->id_Tickets) }}" class="btn btn-primary">Editar &nbsp
                            <i class="fa fa-edit"></i></a>
                            @endif
                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                        <form id="frmDatos" action="{{ route('tickets.destroy', $ticket->id_Tickets) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar &nbsp<i
                                    class="fas fa-trash-alt"></i></button>
                        </form>
                        @endif
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