@extends('adminlte::page')

@section('title', 'Editar Actividad #'.$evento->id_Eventos)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-tasks"></i>&nbsp {{ $evento->id_Eventos }}.&nbsp Actividad del Ticket #{{ $ticket_id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> &nbsp Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('eventos.index', ['ticket_id' => $ticket_id]) }}"><i class="fas fa-tasks"></i>&nbsp Actividades</a></li>
                    <li class="breadcrumb-item active"><i class="fas fa-tasks"></i>&nbsp #{{ $evento->id_Eventos }}
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
                    <form action="{{ route('eventos.update', ['ticket_id' => $evento->ticket_id, 'evento' => $evento->id_Eventos]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <input type="hidden" name="ticket_id" value="{{ $ticket_id }}">
                                <input type="hidden" name="usuario" value="{{ $currentUser->id_RS }}">
                            <div class="form-group">
                                <label for="tipoEvento"><i class="fas fa-tag"></i> &nbsp Tipo de Actividad:</label>
                                <select name="tipoEvento" id="tipoEvento" class="form-control">
                                    @foreach($tiposEvento as $tipoEvento)
                                    <option value="{{ $tipoEvento->id_Clasificacion }}" @if($evento->tipoEvento == $tipoEvento->id_Clasificacion) selected @endif>{{ $tipoEvento->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="descripcion"><i class="fas fa-align-left"></i> &nbsp Descripción:</label>
                                <textarea name="descripcion" id="descripcion" class="form-control">{{ old('descripcion', $evento->descripcion) }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="archivo"><i class="fas fa-file-alt"></i> &nbsp Archivo:</label>
                                <input type="file" name="archivo" id="archivo" class="form-control">
                            </div>
                            @if($evento->archivo)
                            <div class="form-group">
                                <a href="#" onclick="openFileModal('{{ asset('storage/' . $evento->archivo) }}')" class="btn btn-primary btn-sm" data-toggle="modal">
                                    <i class="fas fa-eye"></i> &nbsp Ver Archivo Actual
                                </a>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="fechaEvento"><i class="fas fa-calendar-day"></i> &nbsp Fecha de la Actividad:</label>
                                <input type="datetime-local" name="fechaEvento" id="fechaEvento" class="form-control" value="{{ ($evento->fechaEvento ? \Illuminate\Support\Carbon::parse($evento->fechaEvento)->format('Y-m-d\TH:i') : '') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="fechaInicio"><i class="fas fa-clock"></i> &nbsp Fecha de Inicio:</label>
                                <input type="datetime-local" name="fechaInicio" id="fechaInicio" class="form-control" value="{{ ($evento->fechaInicio ? \Illuminate\Support\Carbon::parse($evento->fechaInicio)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="form-group">
                                <label for="fechaFin"><i class="fas fa-clock"></i> &nbsp Fecha de Fin:</label>
                                <input type="datetime-local" name="fechaFin" id="fechaFin" class="form-control" value="{{ ($evento->fechaFin ? \Illuminate\Support\Carbon::parse($evento->fechaFin)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <div class="form-group" style="flex: 1;">
                                    <label for="costo"><i class="fas fa-coins"></i> &nbsp Costo (VES):</label>
                                    <input type="number" step="0.01" name="costo" id="costo" class="form-control" value="{{ old('costo', $evento->costo) }}" oninput="convertToUSD()">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label for="usdValue"><i class="fas fa-dollar-sign"></i> &nbsp Costo (USD):</label>
                                    <input type="text" id="usdValue" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('eventos.index', ['ticket_id' => $evento->ticket_id]) }}" class="btn btn-secondary"><i class="fa fa-hand-point-left"></i> &nbsp Volver al listado</a>
                            <button type="submit" class="btn btn-primary">Actualizar &nbsp <i class="fa fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalViewFile" tabindex="-1" role="dialog" aria-labelledby="modalViewFileLabel" aria-hidden="true">
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
                        <a id="downloadLink" href="#" class="btn btn-secondary" download><i class="fas fa-download"></i> &nbsp Descargar</a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop

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

    function convertToUSD(costo) {
        if (!costo || costo <= 0) {
            document.getElementById('usdValue').value = '';
            return;
        }
        fetch(`/convert-ves-to-usd?value=${costo}`)
            .then(response => response.json())
            .then(data => {
                if (data.usd) {
                    document.getElementById('usdValue').value = parseFloat(data.usd).toFixed(3);
                } else {
                    document.getElementById('usdValue').value = 'Error';
                }
            })
            .catch(() => {
                document.getElementById('usdValue').value = 'Error';
            });
    }

    $(document).ready(function() {
        var initialCosto = $(costo).val();
        convertToUSD(initialCosto);
        $('#costo').on('input', function() {
            convertToUSD(this.value);
        });
    });
</script>
@endsection

@section('css')
@stop