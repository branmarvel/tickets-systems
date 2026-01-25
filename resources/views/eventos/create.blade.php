@extends('adminlte::page')

@section('title', 'Crear Actividad')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-tasks"></i>&nbsp Actividad para el Ticket #{{ $ticket_id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> &nbsp Home</a></li>
                    <li class="breadcrumb-item active"><a
                            href="{{ route('eventos.index', ['ticket_id' => $ticket_id]) }}"><i
                                class="fas fa-tasks"></i>&nbsp Actividades</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-tasks"></i>&nbsp Crear</li>
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
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('eventos.store', ['ticket_id' => $ticket_id]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <input type="hidden" name="ticket_id" value="{{ $ticket_id }}">
                            <input type="hidden" name="usuario" value="{{ $currentUser->id_RS }}">
                            {{-- <div class="form-group">
                                <label for="usuario"><i class="fas fa-user"></i> &nbsp Usuario:</label>
                                <select name="usuario" id="usuario" class="form-control">
                                    <!-- <option value="" disabled selected>Usuarios</option> -->
                                    @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id_RS }}"
                            {{ $usuario->id_RS == $currentUser->id_RS ? 'selected' : '' }}>
                            {{ $usuario->nombreCompleto }}
                            </option>
                            @endforeach
                            </select>
                        </div> --}}
                        @if (auth()->user()->rol !== 10007)
                        <div class="form-group">
                            <label for="tipoEvento"><i class="fas fa-tag"></i> &nbsp Tipo de Actividad:</label>
                            <select name="tipoEvento" id="tipoEvento" class="form-control" required>
                                <option value="" disabled selected>Tipos de Actividades</option>
                                @foreach ($tiposEvento as $tipoEvento)
                                <option value="{{ $tipoEvento->id_Clasificacion }}">{{ $tipoEvento->titulo }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <input type="hidden" name="tipoEvento" value="10095">
                        @endif
                        <div class="form-group">
                            <label for="descripcion"><i class="fas fa-align-left"></i> &nbsp
                                @if (auth()->user()->rol !== 10007)
                                Descripción:
                                @else
                                Comentario:
                                @endif
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" maxlength="150"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="archivo"><i class="fas fa-file-alt"></i> &nbsp Documentación de la Actividad (Maximo 200MB):</label>
                            <input type="file" name="archivo" id="archivo" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="fechaEvento" id="fechaEvento"
                                value="{{ old('fechaEvento', now()->format('Y-m-d\TH:i')) }}">
                        </div>
                        @if (!in_array(auth()->user()->rol, [10007]))
                        <div class="form-group">
                            <label for="fechaInicio"><i class="fas fa-clock"></i> &nbsp Fecha de Inicio:</label>
                            <input type="datetime-local" name="fechaInicio" id="fechaInicio" class="form-control"
                                value="{{ old('fechaInicio', now()->format('Y-m-d\TH:i')) }}">
                        </div>
                        @endif
                        @if (!in_array(auth()->user()->rol, [10007]))
                        <div class="form-group">
                            <label for="fechaFin"><i class="fas fa-clock"></i> &nbsp Fecha de Fin:</label>
                            <input type="datetime-local" name="fechaFin" id="fechaFin" class="form-control"
                                value="{{ old('fechaFin', now()->format('Y-m-d\TH:i')) }}">
                        </div>
                        @endif
                        @if (!in_array(auth()->user()->rol, [10007]))
                        <div style="display: flex; gap: 1rem;">
                            <div class="form-group" style="flex: 1;">
                                <label for="costo"><i class="fas fa-coins"></i> &nbsp Costo (VES):</label>
                                <input type="number" step="0.01" name="costo" id="costo"
                                    class="form-control" value="0.00">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="usdValue"><i class="fas fa-dollar-sign"></i> &nbsp Costo (USD):</label>
                                <input type="text" id="usdValue" class="form-control" readonly>
                            </div>
                        </div>
                        @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('eventos.index', ['ticket_id' => $ticket_id]) }}"
                        class="btn btn-secondary"><i class="fa fa-hand-point-left"></i> &nbsp Volver al
                        listado</a>
                    <button type="submit" class="btn btn-primary">Guardar &nbsp <i
                            class="fa fa-save"></i></button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</section>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Validación de tamaño de archivo (200MB)
        $('#archivo').on('change', function() {
            const maxSize = 200 * 1024 * 1024; // 200MB en bytes
            if (this.files[0] && this.files[0].size > maxSize) {
                $('#archivo-error').removeClass('d-none').text('El archivo no debe superar los 200MB.');
                this.value = ''; // Resetea el input
            } else {
                $('#archivo-error').addClass('d-none');
            }
        });

        // Conversión de costo de VES a USD
        $('#costo').on('blur', function() {
            let costo = $(this).val();
            if (costo) {
                $.ajax({
                    url: '/convert-ves-to-usd',
                    type: 'GET',
                    data: {
                        value: costo
                    },
                    success: function(data) {
                        $('#usdValue').val(data.usd);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        $('#usdValue').val('Error');
                    }
                });
            } else {
                $('#usdValue').val('');
            }
        });
    });
</script>
@stop

@section('css')
@stop
