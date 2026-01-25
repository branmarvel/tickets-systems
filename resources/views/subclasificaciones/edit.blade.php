@extends('adminlte::page')

@section('title', 'Editar '.$subclasificacion->titulo.' en '.$clasificacion->titulo)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Editar {{ $clasificacion->titulo }} &nbsp;<i class="fa {{ $clasificacion->icono }}"></i></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i> &nbsp; Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('clasificaciones.index') }}"><i class="fas fa-star"></i> &nbsp; Clasificaciones</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('subclasificaciones.index', $clasificacion->id_Clasificacion) }}"><i class="fa {{ $clasificacion->icono ?? 'fa-question-circle' }}"></i> &nbsp; {{ $clasificacion->titulo }}</a></li>
                    <li class="breadcrumb-item active"><i class="fa {{ $subclasificacion->icono }}"></i> {{ $subclasificacion->titulo }}</li>
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
                        <h3 class="card-title">Actualizar Datos &nbsp;<i class="fas fa-edit"></i></h3>
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
                    <form action="{{ route('subclasificaciones.update', [$clasificacion->id_Clasificacion, $subclasificacion->id_Clasificacion]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="titulo"><i class="fas fa-heading"></i>&nbsp; Título:</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $subclasificacion->titulo }}" required maxlength="45">
                            </div>
                            <div class="form-group">
                                <label for="descripcion"><i class="fas fa-align-left"></i>&nbsp; Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" maxlength="150">{{ $subclasificacion->descripcion }}</textarea>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="card-header">
                                            <i id="icon-preview" class="fa {{ $subclasificacion->icono }} fa-5x"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-11">
                                        <label for="icono"><i class="fas fa-icons"></i>&nbsp; Icono:</label>
                                        <select class="form-control" id="icono" name="icono" required>
                                            <option value="{{ $subclasificacion->icono }}" selected>{{ $subclasificacion->icono }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('subclasificaciones.index', $clasificacion->id_Clasificacion) }}" class="btn btn-secondary"><i class="fas fa-hand-point-left"></i>&nbsp; Volver a {{ $clasificacion->titulo }}</a>
                            <button type="submit" class="btn btn-primary">Actualizar &nbsp;<i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/icons')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('icono');
                data.forEach(icon => {
                    const option = document.createElement('option');
                    option.value = icon;
                    option.innerHTML = `<i class="fa ${icon}"></i> ${icon}`;
                    select.appendChild(option);
                });
                const iconPreview = document.getElementById('icon-preview');
                const selectedIcon = select.value;
                iconPreview.className = `fa ${selectedIcon}`;
            });
        document.getElementById('icono').addEventListener('change', function() {
            const iconPreview = document.getElementById('icon-preview');
            const selectedIcon = this.value;
            iconPreview.className = `fa ${selectedIcon}`;
        });
    });
</script>
@endsection

@section('css')
<style>
    #icon-preview {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 80px;
        font-size: 50px;
    }
</style>
@stop
