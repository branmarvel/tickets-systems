@extends('adminlte::page')

@section('title', 'Tickets')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-2">
                <h1><i class="fas fa-ticket-alt"></i>&nbsp Tickets</h1>
            </div>
            <div class="col-sm-4">
                <form action="{{ route('tickets.index') }}" method="GET" class="form-inline">
                    <div class="input-group mb-2 mr-sm-2">
                        <select name="status" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($statuses as $id => $titulo)
                            <option value="{{ $id }}" {{ request('status') == $id ? 'selected' : '' }}>
                                {{ $titulo }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-filter"></i></button>
                </form>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-ticket-alt"></i>&nbsp Tickets</li>
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
                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i>&nbsp Listado de Tickets</h3>
                        <div class="d-flex justify-content-end align-items-center">
                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary">Agregar &nbsp<i class="fas fa-ticket-alt"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tickets-table" class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i>&nbsp ID</th>
                                        <th><i class="fas fa-user"></i>&nbsp Cliente</th>
                                        <th><i class="fas fa-user-cog"></i>&nbsp Técnico</th>
                                        <th><i class="fas fa-box"></i>&nbsp Equipo</th>
                                        <th><i class="fas fa-info-circle"></i>&nbsp Estado</th>
                                        <th><i class="fas fa-calendar-alt"></i>&nbsp Fecha de Solicitud</th>
                                        <th><i class="fas fa-calendar-check"></i>&nbsp Fecha de Asignación</th>
                                        <th><i class="fas fa-calendar-alt"></i>&nbsp Fecha de Diagnóstico</th>
                                        <th><i class="fas fa-calendar-check"></i>&nbsp Fecha de Aprobación</th>
                                        <th><i class="fas fa-tools"></i>&nbsp Fecha de Reparación</th>
                                        <th><i class="fas fa-calendar-check"></i>&nbsp Fecha de Finalización</th>
                                        <th><i class="fas fa-cogs"></i>&nbsp Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id_Tickets }}</td>
                                        <td>{{ $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A' }}</td>
                                        <td>{{ $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A' }}</td>
                                        <td>{{ $ticket->propiedadEquipo->nroSerie . ' (' . $ticket->propiedadEquipo->tipoEquipoClasificacion->titulo . ' - ' . $ticket->propiedadEquipo->marcaClasificacion->titulo . ')' ?? 'N/A' }}
                                        </td>
                                        <td><i
                                                class="fa {{ $ticket->statusClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                            &nbsp {{ $ticket->statusClasificacion->titulo ?? 'N/A' }}</td>
                                        <td>{{ $ticket->fechaSolicitud ? \Illuminate\Support\Carbon::parse($ticket->fechaSolicitud)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td>{{ $ticket->fechaAsignacion ? \Illuminate\Support\Carbon::parse($ticket->fechaAsignacion)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td>{{ $ticket->fechaDiagnostico ? \Illuminate\Support\Carbon::parse($ticket->fechaDiagnostico)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td>{{ $ticket->fechaAprobacion ? \Illuminate\Support\Carbon::parse($ticket->fechaAprobacion)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td>{{ $ticket->fechaReparacion ? \Illuminate\Support\Carbon::parse($ticket->fechaReparacion)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td>{{ $ticket->fechaFinalizacion ? \Illuminate\Support\Carbon::parse($ticket->fechaFinalizacion)->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket->id_Tickets) }}"
                                                class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('eventos.index', ['ticket_id' => $ticket->id_Tickets]) }}"
                                                class="btn btn-info btn-sm"><i class="fas fa-tasks"></i></a>
                                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                                            <a href="{{ route('tickets.edit', $ticket->id_Tickets) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            @endif
                                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                                            <form id="frmDatos"
                                                action="{{ route('tickets.destroy', $ticket->id_Tickets) }}"
                                                method="POST" style="display:inline;">
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
        if (!$.fn.DataTable.isDataTable('#tickets-table')) {
            $('#tickets-table').DataTable({
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
        @if(session('agregado'))
        Swal.fire({
            title: 'Agregado',
            text: "{{ session('agregado') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
        @endif

        @if(session('actualizar'))
        Swal.fire({
            title: 'Actualizado',
            text: "{{ session('actualizar') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
        @endif

        @if(session('success'))
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
