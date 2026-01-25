@extends('adminlte::page')

@section('title', 'Dashboard', 'icon', 'fas fa-home')

@section('content_header')
    <h1><i class="fa {{ $clasificacion->icono ?? 'fa-home' }}"></i> &nbsp Dashboard</h1>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                @if (!in_array(auth()->user()->rol, [10005, 10007]))
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $countRazonSocial }}</h3>
                        <p>Usuarios</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <a href="{{ url('/razon_social') }}" class="small-box-footer">Detalles CLIENTES <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
                @endif
            </div>
            @if (!in_array(auth()->user()->rol, [10005, 10007]))
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $countEquipos }}</h3>
                        <p>Equipos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <a href="{{ url('/equipos') }}" class="small-box-footer">Detalles REPARACIÓN <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $countTickets }}</h3>
                        <p>Tickets</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <a href="{{ url('/tickets') }}" class="small-box-footer">Detalles TICKETS <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $countEventos }}</h3>
                        <p>Actividades</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <a href="{{ url('/eventos') }}" class="small-box-footer">Detalles ACTIVIDADES <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> &nbsp Estatus de Tickets</h3>
                    </div>
                    <div class="card-body" style="height: 765px;">
                        <div style="overflow-x:auto;">
                            <canvas id="statusPolarAreaChart" style="width:100%; height: 70vh;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-lg-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-calendar-alt"></i> &nbsp Calendario</h3>
                    </div>
                    <div class="card-body">
                        <div id="calendar" style="width:100%;"></div>
                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel"><i class="fas fa-clipboard-list"></i>&nbsp Detalles del
                        Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="eventTitle"></p>
                    <p id="eventStart"></p>
                    <p id="eventEnd"></p>
                    <p id="eventDescription"></p>
                    <p id="eventCliente"></p>
                    <p id="eventTecnico"></p>
                    <p id="eventEquipo"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fa fa-hand-point-left"></i>&nbsp Cerrar</button>
                            @if (!in_array(auth()->user()->rol, [10005, 10007]))
                    <a id="editButton" href="#" class="btn btn-primary">Editar &nbsp <i class="fa fa-edit"></i></a>
                    @endif
                    @if (!in_array(auth()->user()->rol, [10005, 10007]))
                    <form id="deleteForm" method="POST" style="display:inline;">
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

@stop

@section('css')
    @stack('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('js')
    @stack('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function() {
            var ctx = document.getElementById('statusPolarAreaChart').getContext('2d');
var statusPolarAreaChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(
            array_map(function ($status) {
                $statusLabels = [
                    10080 => 'Solicitud',
                    10081 => 'Asignación',
                    10082 => 'Diagnóstico',
                    10083 => 'Aprobación',
                    10084 => 'Reparación',
                    10085 => 'Finalización',
                    10086 => 'Cancelado',
                ];
                return $statusLabels[$status] ?? 'Desconocido';
            }, array_keys($statusCounts)),
        ) !!},
        datasets: [{
            label: 'Tickets por Estatus',
            data: {!! json_encode(array_values($statusCounts)) !!},
            backgroundColor: [
                '#7C4DFF', '#FF9800', '#3F51B5', '#4CAF50',
                '#808080', '#002366', '#000000'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,  // Permite ajuste de altura en móviles
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        size: 12 // Ajusta el tamaño del texto en móviles
                    }
                }
            }
        }
    }
});
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                locale: 'es',
                editable: true,
                droppable: true,
                events: '/api/tickets',
                eventRender: function(event, element) {
                    switch (event.type) {
                        case 'solicitud':
                            element.css('background-color', '#7C4DFF');
                            break;
                        case 'asignacion':
                            element.css('background-color', '#FF9800');
                            break;
                        case 'diagnostico':
                            element.css('background-color', '#3F51B5');
                            break;
                        case 'aprobacion':
                            element.css('background-color', '#4CAF50');
                            break;
                        case 'reparacion':
                            element.css('background-color', '#808080');
                            break;
                        case 'finalizacion':
                            element.css('background-color', '#002366');
                            break;
                        default:
                            element.css('background-color', '#000000');
                            break;
                    }
                },
                eventClick: function(event, jsEvent, view) {
                    $('#eventTitle').html('<i class="fas fa-tag"></i> Título: ' + event.title);
                    $('#eventStart').html('<i class="fas fa-calendar-day"></i> Fecha de Inicio: ' +
                        event.start.format('YYYY-MM-DD HH:mm:ss'));
                    $('#eventEnd').html('<i class="fas fa-calendar-day"></i> Fecha de Fin: ' + (event
                        .end ? event.end.format('YYYY-MM-DD HH:mm:ss') : event.start.format(
                            'YYYY-MM-DD HH:mm:ss')));
                    $('#eventDescription').html('<i class="fas fa-info-circle"></i> Estado: ' + event
                        .description);
                    $('#eventCliente').html('<i class="fas fa-user"></i> Cliente: ' + event.cliente);
                    $('#eventTecnico').html('<i class="fas fa-user-cog"></i> Técnico: ' + event
                        .tecnico);
                    $('#eventEquipo').html('<i class="fas fa-box"></i> Equipo: ' + event.equipo);
                    $('#editButton').attr('href', '/tickets/' + event.id + '/edit');
                    $('#deleteForm').attr('action', '/tickets/' + event.id);
                    $('#eventModal').modal('show');
                },
                eventDrop: function(event, delta, revertFunc) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres cambiar la fecha de esta actividad?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateEvent(event);
                        } else {
                            revertFunc();
                        }
                    });
                },
                eventResize: function(event, delta, revertFunc) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Quieres cambiar la duración de esta actividad?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateEvent(event);
                        } else {
                            revertFunc();
                        }
                    });
                },
            });

            @if (session('success'))
                Swal.fire({
                    title: 'Eliminado',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            // SweetAlert2 para confirmación de eliminación
            $('form#deleteForm').on('submit', function(e) {
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

            function updateEvent(event) {
                var eventData = {
                    id: event.id,
                    start: event.start.format(),
                    end: event.end ? event.end.format() : null,
                    type: event.type
                };
                $.ajax({
                    url: '/tickets/update-event',
                    method: 'POST',
                    data: eventData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Actividad actualizada',
                            text: 'La actividad se ha actualizado con éxito.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un error al actualizar la actividad.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    </script>
@stop
