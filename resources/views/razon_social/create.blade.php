@extends('adminlte::page')

@section('title', 'Crear Entidad')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fa fa-user-plus"></i>&nbsp Crear Entidad</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fa fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('razon_social.index') }}"><i class="fa fa-user-plus"></i>&nbsp Entidades</a></li>
                    <li class="breadcrumb-item active">Crear&nbsp<i class="fa fa-user-plus"></i></li>
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
                        <h3 class="card-title">Datos de la Entidad&nbsp<i class="fa fa-user-plus"></i></h3>
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
                    <form action="{{ route('razon_social.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombreCompleto"><i class="fas fa-user"></i>&nbsp Nombre Completo:</label>
                                <input type="text" name="nombreCompleto" class="form-control" 
                                       required pattern="^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{1,45}$" 
                                       maxlength="45" title="Solo letras, máximo 45 caracteres">
                            </div>
                            <div class="form-group">
                                <label for="tipoPersona"><i class="fas fa-id-badge"></i>&nbsp Tipo Persona:</label>
                                <select name="tipoPersona" class="form-control" required>
                                    <option value="" disabled selected>Tipos</option>
                                    @foreach($tipoPersonas as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nroIdentificacion"><i class="fas fa-id-card"></i>&nbsp Nro Identificación:</label>
                                <input type="number" name="nroIdentificacion" class="form-control" 
                                       required min="4000000" max="9999999999"
                                       title="Nro Identificación debe ser un minimo 4.000.000 y maximo de 9.999.999.999">
                            </div>
                            <div class="form-group">
                                <label for="telefono"><i class="fas fa-phone"></i>&nbsp Teléfono:</label>
                                <input type="text" name="telefono" class="form-control" 
                                       required pattern="^\d{10,11}$" 
                                       minlength="10" maxlength="11" 
                                       title="Solo números, mínimo 10 y máximo 11 dígitos">
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i>&nbsp Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="direccion"><i class="fas fa-map-marker-alt"></i>&nbsp Dirección Completa:</label>
                                <input type="text" id="direccion" name="direccion" class="form-control" required>
                            </div>
                            <div id="map" style="height: 400px; width: 100%;"></div>                                                                                  
                            @if (auth()->user()->rol !== 10006) <!-- Si no es operador -->
                            <div class="form-group">
                                <label for="rol"><i class="fas fa-user-tag"></i>&nbsp Rol:</label>
                                <select name="rol" class="form-control" required>
                                    <option value="" disabled selected>Roles</option>
                                    @foreach($roles as $id => $titulo)
                                    <option value="{{ $id }}">{{ $titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else <!-- Si es operador, solo asignar cliente -->
                            <input type="hidden" name="rol" value="10007">
                            @endif
                            <div class="form-group">
                                <label for="contraseña"><i class="fas fa-key"></i>&nbsp;Contraseña:</label>
                                <input type="password" name="contraseña" id="contraseña" class="form-control" required minlength="8">
                            </div>
                            <div class="form-group">
                                <label for="confirmarContraseña"><i class="fas fa-key"></i>&nbsp;Confirmar Contraseña:</label>
                                <input type="password" name="contraseña_confirmation" id="confirmarContraseña" class="form-control" required minlength="8">
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="verContraseña">
                                <label class="form-check-label" for="verContraseña">Mostrar Contraseña</label>
                            </div>
                            
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('razon_social.index') }}" class="btn btn-secondary"><i class="fas fa-hand-point-left"></i>&nbsp Volver al listado</a>
                            <button type="submit" class="btn btn-primary">Guardar&nbsp<i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@stop


@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inicializa el mapa centrado en Caracas
        let map = L.map('map').setView([10.4806, -66.9036], 13);

        // Añade la capa de mosaicos de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; Servi Aires'
        }).addTo(map);

        // Añade un marcador arrastrable en Caracas
        let marker = L.marker([10.4806, -66.9036], {draggable: true}).addTo(map);

        // Función para hacer la geocodificación inversa y obtener la dirección exacta
        function geocodeLatLng(lat, lng) {
            let url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        const road = data.address.road ? data.address.road : 'Sin nombre de calle';
                        const suburb = data.address.suburb ? data.address.suburb : '';
                        const city = data.address.city ? data.address.city : '';
                        const state = data.address.state ? data.address.state : '';
                        const country = data.address.country ? data.address.country : '';

                        // Combina todos los datos en una dirección completa
                        const fullAddress = `${road}, ${suburb}, ${city}, ${state}, ${country}`;

                        // Actualiza el campo de dirección con la dirección completa
                        document.getElementById('direccion').value = fullAddress;
                    } else {
                        document.getElementById('direccion').value = 'Dirección no encontrada';
                    }
                })
                .catch(error => {
                    console.error('Error en la geocodificación inversa:', error);
                    document.getElementById('direccion').value = 'Error al obtener la dirección';
                });
        }

        // Evento que se dispara cuando el marcador se mueve
        marker.on('dragend', function (e) {
            const latLng = marker.getLatLng();
            geocodeLatLng(latLng.lat, latLng.lng); // Llama a la función de geocodificación inversa
        });


        document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('verContraseña');
    const contraseñaInput = document.getElementById('contraseña');
    const confirmarContraseñaInput = document.getElementById('confirmarContraseña');
    const submitButton = document.getElementById('submitButton');

    // Mostrar/ocultar contraseñas al cambiar el checkbox
    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            contraseñaInput.type = 'text';
            confirmarContraseñaInput.type = 'text';
        } else {
            contraseñaInput.type = 'password';
            confirmarContraseñaInput.type = 'password';
        }
    });
});
    </script>
@stop


