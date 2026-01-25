@extends('adminlte::page')

@section('title', 'Editar Ticket #'.$ticket->id_Tickets)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-ticket-alt"></i>&nbsp Editar Ticket #{{ $ticket->id_Tickets }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}"><i class="fas fa-ticket-alt"></i>&nbsp Tickets</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-ticket-alt"></i>&nbsp #{{ $ticket->id_Tickets }}</li>
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
                        <h3 class="card-title"><i class="fas fa-ticket-alt"></i>&nbsp Datos del Ticket</h3>
                    </div>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('tickets.update', $ticket->id_Tickets) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="card-body">
                            <div class="form-group form-row">
                                <div class="col-md-6">
                                    <label for="nroIdentificacion"><i class="fas fa-id-card"></i>&nbsp Número de Identificación del Cliente:</label>
                                    <input type="text" id="nroIdentificacion" name="nroIdentificacion" class="form-control" value="{{ $ticket->clienteRazonSocial->nroIdentificacion }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="clienteNombre"><i class="fas fa-user"></i>&nbsp Nombre del Cliente:</label>
                                    <input type="text" id="clienteNombre" class="form-control" value="{{ $ticket->clienteRazonSocial->nombreCompleto }}" readonly>
                                </div>
                                <input type="hidden" id="id_RS" name="cliente" value="{{ $ticket->clienteRazonSocial->id_RS }}">
                            </div>
                            <div class="form-group">
                                <label for="tecnAsignado" class="form-label"><i class="fas fa-user-cog"></i>&nbsp Técnico Asignado:</label>
                                <select name="tecnAsignado" id="tecnAsignado" class="form-control" required>
                                    @foreach ($tecnicos as $id => $nombre)
                                    <option value="{{ $id }}" {{ $ticket->tecnAsignado == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="equipo" class="form-label"><i class="fas fa-box"></i>&nbsp Equipo:</label>
                                <input type="text" class="form-control" value="{{ $equipos[$ticket->propiedadEquipo->id_Equipos] }}" readonly>
                                <select name="equipo" id="equipo" class="form-control" style="display:none;">
                                    @foreach ($equipos as $id => $info)
                                    <option value="{{ $id }}" {{ $ticket->propiedadEquipo && $ticket->propiedadEquipo->id_Equipos == $id ? 'selected' : '' }}>
                                        {{ $info }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status" class="form-label"><i class="fas fa-info-circle"></i>&nbsp Estatus:</label>
                                <select name="status" id="status" class="form-control" required>
                                    @foreach ($statuses as $id => $titulo)
                                    <option value="{{ $id }}" {{ $ticket->status == $id ? 'selected' : '' }}>{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fechaSolicitud" class="form-label"><i class="fas fa-calendar-alt"></i>&nbsp Fecha de Solicitud:</label>
                                <input type="datetime-local" name="fechaSolicitud" id="fechaSolicitud" class="form-control" value="{{ ($ticket->fechaSolicitud ? \Illuminate\Support\Carbon::parse($ticket->fechaSolicitud)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="form-group" id="fechaAsignacionGroup" style="display:none;">
                                <label for="fechaAsignacion"><i class="fas fa-calendar-check"></i>&nbsp Fecha de Asignación:</label>
                                <input type="datetime-local" name="fechaAsignacion" id="fechaAsignacion" class="form-control" value="{{ ($ticket->fechaAsignacion ? \Illuminate\Support\Carbon::parse($ticket->fechaAsignacion)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="form-group" id="fechaDiagnosticoGroup" style="display:none;">
                                <label for="fechaDiagnostico"><i class="fas fa-calendar-day"></i>&nbsp Fecha de Diagnóstico:</label>
                                <input type="datetime-local" name="fechaDiagnostico" id="fechaDiagnostico" class="form-control" value="{{ ($ticket->fechaDiagnostico ? \Illuminate\Support\Carbon::parse($ticket->fechaDiagnostico)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="form-group" id="fechaAprobacionGroup" style="display:none;">
                                <label for="fechaAprobacion"><i class="fas fa-calendar-check"></i>&nbsp Fecha de Aprobación:</label>
                                <input type="datetime-local" name="fechaAprobacion" id="fechaAprobacion" class="form-control" value="{{ ($ticket->fechaAprobacion ? \Illuminate\Support\Carbon::parse($ticket->fechaAprobacion)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="form-group" id="fechaReparacionGroup" style="display:none;">
                                <label for="fechaReparacion"><i class="fas fa-tools"></i>&nbsp Fecha de Reparación:</label>
                                <input type="datetime-local" name="fechaReparacion" id="fechaReparacion" class="form-control" value="{{ ($ticket->fechaReparacion ? \Illuminate\Support\Carbon::parse($ticket->fechaReparacion)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="form-group" id="fechaFinalizacionGroup" style="display:none;">
                                <label for="fechaFinalizacion"><i class="fas fa-calendar-check"></i>&nbsp Fecha de Finalización:</label>
                                <input type="datetime-local" name="fechaFinalizacion" id="fechaFinalizacion" class="form-control" value="{{ ($ticket->fechaFinalizacion ? \Illuminate\Support\Carbon::parse($ticket->fechaFinalizacion)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i class="fa fa-hand-point-left"></i>&nbsp Volver al listado</a>
                            <button type="submit" class="btn btn-primary">Actualizar &nbsp <i class="fa fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

@section('js')
<script>
    $(document).ready(function() {
    // Restricción de fechas máximas y relaciones entre campos
    const oneYearFromNow = new Date();
    oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1);

    // Configurar límites de fechas
    $('input[type="datetime-local"]').each(function() {
        const now = new Date().toISOString().slice(0, 16);
        $(this).attr('min', now);
        $(this).attr('max', oneYearFromNow.toISOString().slice(0, 16));
    });

    // Verificar dependencias entre fechas
    const dateFields = [
        '#fechaAsignacion',
        '#fechaDiagnostico',
        '#fechaAprobacion',
        '#fechaReparacion',
        '#fechaFinalizacion',
    ];

    dateFields.forEach((field, index) => {
        $(field).on('change', function() {
            const currentDate = new Date($(this).val());
            const previousField = dateFields[index - 1];
            const nextField = dateFields[index + 1];

            // Validar si la fecha seleccionada es anterior a la anterior
            if (previousField && $(previousField).val()) {
                const previousDate = new Date($(previousField).val());
                if (currentDate < previousDate) {
                    alert(`La fecha de ${$(field).prev('label').text().trim()} no puede ser anterior a la fecha de ${$(previousField).prev('label').text().trim()}`);
                    $(this).val(''); // Limpiar la fecha si no es válida
                }
            }

            // Validar si la fecha seleccionada es posterior a la siguiente
            if (nextField && $(nextField).val()) {
                const nextDate = new Date($(nextField).val());
                if (currentDate > nextDate) {
                    alert(`La fecha de ${$(field).prev('label').text().trim()} no puede ser posterior a la fecha de ${$(nextField).prev('label').text().trim()}`);
                    $(nextField).val(''); // Limpiar la siguiente fecha si no es válida
                }
            }
        });
    });

    // Configurar el cambio en el campo de estado
    $('#status').change(function() {
        var status = $(this).val();
        var now = new Date().toISOString().slice(0, 16);

        // Ocultar todos los campos de fecha
        hideAllDateFields();

        // Mostrar y establecer el valor de los campos de fecha según el estado
        if (status === '10080') {
            // No se muestran campos de fecha para este estado
        } else if (status === '10081') {
            showDateField('fechaAsignacionGroup');
            $('#fechaAsignacion').val($('#fechaAsignacion').val() || now);
        } else if (status === '10082') {
            showDateField('fechaAsignacionGroup');
            showDateField('fechaDiagnosticoGroup');
            $('#fechaDiagnostico').val($('#fechaDiagnostico').val() || now);
        } else if (status === '10083') {
            showDateField('fechaAsignacionGroup');
            showDateField('fechaDiagnosticoGroup');
            showDateField('fechaAprobacionGroup');
            $('#fechaAprobacion').val($('#fechaAprobacion').val() || now);
        } else if (status === '10084') {
            showDateField('fechaAsignacionGroup');
            showDateField('fechaDiagnosticoGroup');
            showDateField('fechaAprobacionGroup');
            showDateField('fechaReparacionGroup');
            $('#fechaReparacion').val($('#fechaReparacion').val() || now);
        } else if (status === '10085') {
            showDateField('fechaAsignacionGroup');
            showDateField('fechaDiagnosticoGroup');
            showDateField('fechaAprobacionGroup');
            showDateField('fechaReparacionGroup');
            showDateField('fechaFinalizacionGroup');
            $('#fechaFinalizacion').val($('#fechaFinalizacion').val() || now);
        }
    });

    // Funciones para mostrar y ocultar campos de fecha
    function showDateField(fieldId) {
        $('#' + fieldId).show();
    }

    function hideAllDateFields() {
        $('#fechaAsignacionGroup').hide();
        $('#fechaDiagnosticoGroup').hide();
        $('#fechaAprobacionGroup').hide();
        $('#fechaReparacionGroup').hide();
        $('#fechaFinalizacionGroup').hide();
    }

    var initialStatus = $('#status').val();
    if (initialStatus) {
        $('#status').trigger('change');
    }
});

</script>
@stop

@section('css')
@stop