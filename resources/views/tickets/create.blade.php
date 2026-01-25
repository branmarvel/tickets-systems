@extends('adminlte::page')

@section('title', 'Crear Ticket')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-ticket-alt"></i>&nbsp Crear Ticket</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}"><i class="fas fa-ticket-alt"></i>&nbsp Tickets</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-ticket-alt"></i>&nbsp Crear</li>
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
                    <form action="{{ route('tickets.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group form-row">
                                <div class="col-md-6">
                                    <label for="nroIdentificacion"><i class="fas fa-id-card"></i>&nbsp Número de Identificación del Cliente:</label>
                                    <input type="text" id="nroIdentificacion" name="nroIdentificacion" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="clienteNombre"><i class="fas fa-user"></i>&nbsp Nombre del Cliente:</label>
                                    <input type="text" id="clienteNombre" class="form-control" readonly>
                                </div>
                                <input type="hidden" id="id_RS" name="cliente">
                            </div>
                            <div class="form-group">
                                <label for="tecnAsignado" class="form-label"><i class="fas fa-user-cog"></i>&nbsp Técnico Asignado:</label>
                                <select name="tecnAsignado" id="tecnAsignado" class="form-control" required>
                                    <!-- <option value="" disabled selected>Técnicos</option> -->
                                    @foreach ($tecnicos as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="equipo" class="form-label"><i class="fas fa-box"></i>&nbsp Equipo:</label>
                                <select name="equipo" id="equipo" class="form-control">
                                    <option value="" disabled selected>Equipos</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status" class="form-label"><i class="fas fa-info-circle"></i>&nbsp Estatus:</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="" disabled selected>Estatus</option>
                                    @foreach ($statuses as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fechaSolicitud" class="form-label"><i class="fas fa-calendar-alt"></i>&nbsp Fecha de Solicitud:</label>
                                <input type="datetime-local" name="fechaSolicitud" id="fechaSolicitud" class="form-control" value="{{ old('fechaSolicitud', now()->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="form-group" id="fechaAsignacionGroup" style="display:none;">
                                <label for="fechaAsignacion"><i class="fas fa-calendar-check"></i>&nbsp Fecha de Asignación:</label>
                                <input type="datetime-local" name="fechaAsignacion" id="fechaAsignacion" class="form-control" value="{{ old('fechaAsignacion') }}">
                            </div>
                            <div class="form-group" id="fechaDiagnosticoGroup" style="display:none;">
                                <label for="fechaDiagnostico"><i class="fas fa-calendar-alt"></i>&nbsp Fecha de Diagnóstico:</label>
                                <input type="datetime-local" name="fechaDiagnostico" id="fechaDiagnostico" class="form-control" value="{{ old('fechaDiagnostico') }}">
                            </div>
                            <div class="form-group" id="fechaAprobacionGroup" style="display:none;">
                                <label for="fechaAprobacion"><i class="fas fa-calendar-check"></i>&nbsp Fecha de Aprobación:</label>
                                <input type="datetime-local" name="fechaAprobacion" id="fechaAprobacion" class="form-control" value="{{ old('fechaAprobacion') }}">
                            </div>
                            <div class="form-group" id="fechaReparacionGroup" style="display:none;">
                                <label for="fechaReparacion"><i class="fas fa-tools"></i>&nbsp Fecha de Reparación:</label>
                                <input type="datetime-local" name="fechaReparacion" id="fechaReparacion" class="form-control" value="{{ old('fechaReparacion') }}">
                            </div>
                            <div class="form-group" id="fechaFinalizacionGroup" style="display:none;">
                                <label for="fechaFinalizacion"><i class="fas fa-calendar-check"></i>&nbsp Fecha de Finalización:</label>
                                <input type="datetime-local" name="fechaFinalizacion" id="fechaFinalizacion" class="form-control" value="{{ old('fechaFinalizacion') }}">
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary"><i class="fa fa-hand-point-left"></i>&nbsp Volver al listado</a>
                            <button type="submit" class="btn btn-primary">Guardar &nbsp <i class="fa fa-save"></i></button>
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

                if (previousField && $(previousField).val()) {
                    const previousDate = new Date($(previousField).val());
                    if (currentDate < previousDate) {
                        alert(`La fecha de ${$(field).prev('label').text().trim()} no puede ser anterior a la fecha de ${$(previousField).prev('label').text().trim()}`);
                        $(this).val('');
                    }
                }

                if (nextField && $(nextField).val()) {
                    const nextDate = new Date($(nextField).val());
                    if (currentDate > nextDate) {
                        alert(`La fecha de ${$(field).prev('label').text().trim()} no puede ser posterior a la fecha de ${$(nextField).prev('label').text().trim()}`);
                        $(nextField).val('');
                    }
                }
            });
        });

        // Resto del código existente
        $('#nroIdentificacion').on('blur', function() {
            var nroIdentificacion = $(this).val();
            $.ajax({
                url: '/dueño/get',
                type: 'GET',
                data: {
                    nroIdentificacion: nroIdentificacion
                },
                success: function(data) {
                    console.log("Datos del cliente:", data);
                    if (data.id_RS) {
                        $('#clienteNombre').val(data.nombreCompleto);
                        $('#id_RS').val(data.id_RS);
                        cargarEquipos(data.id_RS);
                    } else {
                        $('#clienteNombre').val('Cliente no encontrado o no válido');
                        $('#id_RS').val('');
                        $('#equipo').empty();
                        $('#equipo').append("<option value='' disabled selected>Equipos</option>");
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        function cargarEquipos(clienteId) {
            $.ajax({
                url: "{{ route('tickets.equiposPorCliente', ['idCliente' => ':idCliente']) }}".replace(':idCliente', clienteId),
                type: 'GET',
                success: function(response) {
                    console.log("Respuesta de equipos:", response);
                    $('#equipo').empty();
                    $('#equipo').append("<option value='' disabled selected>Equipos</option>");
                    $.each(response, function(id, equipo_info) {
                        $('#equipo').append("<option value='" + id + "'>" + equipo_info + "</option>");
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        $('#status').change(function() {
            var status = $(this).val();
            hideAllDateFields();
            var now = new Date().toISOString().slice(0, 16);
            if (status === '10080') {} else if (status === '10081') {
                showDateField('fechaAsignacionGroup');
                $('#fechaAsignacion').val(now);
            } else if (status === '10082') {
                showDateField('fechaAsignacionGroup');
                showDateField('fechaDiagnosticoGroup');
                $('#fechaDiagnostico').val(now);
            } else if (status === '10083') {
                showDateField('fechaAsignacionGroup');
                showDateField('fechaDiagnosticoGroup');
                showDateField('fechaAprobacionGroup');
                $('#fechaAprobacion').val(now);
            } else if (status === '10084') {
                showDateField('fechaAsignacionGroup');
                showDateField('fechaDiagnosticoGroup');
                showDateField('fechaAprobacionGroup');
                showDateField('fechaReparacionGroup');
                $('#fechaReparacion').val(now);
            } else if (status === '10085') {
                showDateField('fechaAsignacionGroup');
                showDateField('fechaDiagnosticoGroup');
                showDateField('fechaAprobacionGroup');
                showDateField('fechaReparacionGroup');
                showDateField('fechaFinalizacionGroup');
                $('#fechaFinalizacion').val(now);
            }
        });

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
    });
</script>
@stop

@section('css')
@stop