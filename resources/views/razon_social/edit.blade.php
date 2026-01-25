@extends('adminlte::page')

@section('title', 'Editar '.$razonSocial->nombreCompleto)

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user"></i>&nbsp Editar {{ $razonSocial->nombreCompleto }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="fa fa-home"></i>&nbsp Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('razon_social.index') }}"><i class="fa fa-user-plus"></i>&nbsp Razones Sociales</a></li>
                    <li class="breadcrumb-item active"><i class="fa fa-user"></i>&nbsp {{ $razonSocial->nombreCompleto }}</li>
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
                        <h3 class="card-title"><i class="fas fa-edit"></i>&nbsp Actualizar Datos</h3>
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
                    <form action="{{ route('razon_social.update', $razonSocial->id_RS) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombreCompleto"><i class="fas fa-user"></i>&nbsp Nombre Completo:</label>
                                <input type="text" name="nombreCompleto" class="form-control" 
                                required pattern="^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{1,45}$" 
                                maxlength="45" title="Solo letras, máximo 45 caracteres" value="{{ old('nombreCompleto', $razonSocial->nombreCompleto) }}">
                            </div>
                            <div class="form-group">
                                <label for="tipoPersona"><i class="fas fa-id-badge"></i>&nbsp Tipo de Persona:</label>
                                <select name="tipoPersona" class="form-control">
                                    @foreach($tipoPersonas as $id => $nombre)
                                    <option value="{{ $id }}" {{ $razonSocial->tipoPersona == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nroIdentificacion"><i class="fas fa-id-card"></i>&nbsp Nro de Identificación:</label>
                                <input type="number" name="nroIdentificacion" class="form-control" 
                                required min="4000000" max="9999999999"
                                title="Nro Identificación debe ser un minimo 4.000.000 y maximo de 9.999.999.999" value="{{ old('nroIdentificacion', $razonSocial->nroIdentificacion) }}">
                            </div>
                            <div class="form-group">
                                <label for="telefono"><i class="fas fa-phone"></i>&nbsp Teléfono:</label>
                                <input type="text" name="telefono" class="form-control" 
                                required pattern="^\d{10,11}$" 
                                minlength="10" maxlength="11" 
                                title="Solo números, mínimo 10 y máximo 11 dígitos" value="{{ old('telefono', $razonSocial->telefono) }}">
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i>&nbsp Email:</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email', $razonSocial->email) }}">
                            </div>
                            <div class="form-group">
                                <label for="direccion"><i class="fas fa-map-marker-alt"></i>&nbsp Dirección Completa:</label>
                                <input type="text" id="direccion" name="direccion" class="form-control" required value="{{ old('direccion', $razonSocial->direccion) }}">
                            </div>
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            <div class="form-group">
                                <label for="rol"><i class="fas fa-user-tag"></i>&nbsp Rol:</label>
                                <select name="rol" class="form-control">
                                    @foreach($roles as $id => $nombre)
                                    <option value="{{ $id }}" {{ $razonSocial->rol == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contraseña"><i class="fas fa-key"></i>&nbsp Contraseña:</label>
                                <input type="password" name="contraseña" class="form-control" required minlength="8" value="{{ old('contraseña', $razonSocial->contraseña) }}">
                            </div>
                            <div class="form-group">
                                <label for="confirmarContraseña"><i class="fas fa-key"></i>&nbsp;Confirmar Contraseña:</label>
                                <input type="password" name="contraseña_confirmation" id="confirmarContraseña" class="form-control" required minlength="8" value="{{ old('contraseña', $razonSocial->contraseña) }}">
                            </div>
                            
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('razon_social.index') }}" class="btn btn-secondary"><i class="fas fa-hand-point-left"></i>&nbsp Volver al listado</a>
                            <button type="submit" class="btn btn-primary">Actualizar&nbsp<i class="fas fa-save"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@stop

@section('js')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inicializa el mapa centrado en la ubicación actual de la entidad
        let lat = {{ $razonSocial->latitud ?? '10.4806' }};  // Usa la latitud guardada o una por defecto
        let lng = {{ $razonSocial->longitud ?? '-66.9036' }};  // Usa la longitud guardada o una por defecto
        let map = L.map('map').setView([lat, lng], 13);

        // Añade la capa de mosaicos de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; Servi Aires'
        }).addTo(map);

        // Añade un marcador arrastrable en la ubicación actual
        let marker = L.marker([lat, lng], {draggable: true}).addTo(map);

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
            geocodeLatLng(latLng.lat, latLng.lng);  // Llama a la función de geocodificación inversa
        });
    </script>
@stop
