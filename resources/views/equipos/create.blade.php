@extends('adminlte::page')

@section('title', 'Crear Equipo')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fa fa-boxes"></i>&nbsp Crear Equipo</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('equipos.index') }}"><i class="fas fa-boxes"></i>&nbsp Equipos</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-plus"></i>&nbsp Crear</li>
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
                        <h3 class="card-title"><i class="fa fa-box"></i>&nbsp Datos del Equipo</h3>
                    </div>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-triangle"></i>&nbsp {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('equipos.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="descripcion"><i class="fas fa-align-left"></i>&nbsp Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" maxlength="150"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="marca"><i class="fas fa-tags"></i>&nbsp Marca:</label>
                                <select name="marca" class="form-control" required>
                                    <option value="" disabled selected>Marcas</option>
                                    @foreach($marcas as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="modelo"><i class="fas fa-cog"></i>&nbsp Modelo:</label>
                                <input type="text" name="modelo" class="form-control" required maxlength="45">
                            </div>
                            <div class="form-group">
                                <label for="nroSerie"><i class="fas fa-hashtag"></i>&nbsp Nro de Serie:</label>
                                <input type="text" name="nroSerie" class="form-control" required maxlength="45">
                            </div>
                            <div class="form-group">
                                <label for="linea"><i class="fas fa-plug"></i>&nbsp Línea:</label>
                                <select name="linea" class="form-control" required>
                                    <option value="" disabled selected>Líneas</option>
                                    @foreach($lineas as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipoEquipo"><i class="fas fa-box-open"></i>&nbsp Tipo de Equipo:</label>
                                <select name="tipoEquipo" class="form-control" required>
                                    <option value="" disabled selected>Tipos</option>
                                    @foreach($tipoEquipo as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="precio"><i class="fas fa-coins"></i>&nbsp Precio (REF):</label>
                                <input type="number" step="0.01" name="precio" class="form-control" value="0.00">
                            </div>
                            <div class="form-group">
                                <label for="motivo"><i class="fas fa-question-circle"></i>&nbsp Motivo:</label>
                                <select name="motivo" class="form-control" required>
                                    <option value="" disabled selected>Motivos</option>
                                    @foreach($motivos as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group form-row">
                                <div class="col-md-6">
                                    <label for="nroIdentificacion"><i class="fas fa-id-card"></i>&nbsp Número de Identificación del Dueño:</label>
                                    <input type="text" id="nroIdentificacion" name="nroIdentificacion" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="clienteNombre"><i class="fas fa-user"></i>&nbsp Nombre del Cliente:</label>
                                    <input type="text" id="clienteNombre" class="form-control" readonly>
                                </div>
                                <input type="hidden" id="id_RS" name="id_RS">
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('equipos.index') }}" class="btn btn-secondary"><i class="fa fa-hand-point-left"></i>&nbsp Volver al listado</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@section('js')
<script>
    $(document).ready(function() {
        $('#nroIdentificacion').on('blur', function() {
            var nroIdentificacion = $(this).val();
            $.ajax({
                url: '/dueño/get',
                type: 'GET',
                data: {
                    nroIdentificacion: nroIdentificacion
                },
                success: function(data) {
                    if (data.id_RS) {
                        $('#clienteNombre').val(data.nombreCompleto);
                        $('#id_RS').val(data.id_RS);
                    } else {
                        $('#clienteNombre').val('Cliente no encontrado o no válido');
                        $('#id_RS').val('');
                    }
                }
            });
        });
    });
</script>
@stop
@endsection
