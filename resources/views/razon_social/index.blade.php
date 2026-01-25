@extends('adminlte::page')

@section('title', 'Entidades')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fa fa-user-plus"></i> &nbsp Entidades</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/dashboard"><i class="fas fa-home"></i>&nbsp Home</a></li>
                        <li class="breadcrumb-item active"><i class="fa fa-user-plus"></i> &nbsp Entidades</li>
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
                            <h3 class="card-title"><i class="fas fa-clipboard-list"></i>&nbsp Listado de Entidades</h3>
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="{{ route('razon_social.create') }}" class="btn btn-primary">Agregar &nbsp<i
                                        class="fa fa-user-plus"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="razon-social-table" class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i>&nbsp ID</th>
                                            <th><i class="fas fa-user"></i>&nbsp Nombre Completo</th>
                                            <th><i class="fas fa-id-badge"></i>&nbsp Tipo Persona</th>
                                            <th><i class="fas fa-id-card"></i>&nbsp Nro Identificación</th>
                                            <th><i class="fas fa-phone"></i>&nbsp Teléfono</th>
                                            <th><i class="fas fa-envelope"></i>&nbsp Email</th>
                                            <th><i class="fas fa-map-marker-alt"></i>&nbsp Dirección</th>
                                            <th><i class="fas fa-user-tag"></i>&nbsp Rol</th>
                                            <th><i class="fas fa-cogs"></i>&nbsp Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($razonSocials as $razonSocial)
                                            <tr>
                                                <td>{{ $razonSocial->id_RS }}</td>
                                                <td>{{ $razonSocial->nombreCompleto }}</td>
                                                <td><i
                                                        class="fa {{ $razonSocial->tipoPersonaClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $razonSocial->tipoPersonaClasificacion->titulo ?? 'N/A' }}
                                                </td>
                                                <td>{{ $razonSocial->nroIdentificacion }}</td>
                                                <td>{{ $razonSocial->telefono }}</td>
                                                <td>{{ $razonSocial->email }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $razonSocial->direccion }}</span>
                                                        <button class="btn btn-primary btn-sm btn-ubicacion ml-2"
                                                            data-direccion="{{ $razonSocial->direccion }}"
                                                            data-toggle="modal" data-target="#modalMapa">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td><i
                                                        class="fa {{ $razonSocial->rolClasificacion->icono ?? 'fa-question-circle' }}"></i>
                                                    &nbsp {{ $razonSocial->rolClasificacion->titulo ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('razon_social.show', $razonSocial->id_RS) }}"
                                                        class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ route('razon_social.edit', $razonSocial->id_RS) }}"
                                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <form id="frmDatos"
                                                        action="{{ route('razon_social.destroy', $razonSocial->id_RS) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i
                                                                class="fas fa-trash-alt"></i></button>
                                                    </form>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <!-- Modal para el mapa -->
                                <div class="modal fade" id="modalMapa" tabindex="-1" role="dialog"
                                    aria-labelledby="modalMapaLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalMapaLabel">Ubicación en el mapa</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="map" style="height: 400px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <script>
        $(document).ready(function() {
            // Inicializar DataTable si no ha sido inicializada
            if (!$.fn.DataTable.isDataTable('#razon-social-table')) {
                $('#razon-social-table').DataTable({
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
            @if (session('agregado'))
                Swal.fire({
                    title: 'Agregada',
                    text: "{{ session('agregado') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('actualizar'))
                Swal.fire({
                    title: 'Actualizada',
                    text: "{{ session('actualizar') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('success'))
                Swal.fire({
                    title: 'Eliminada',
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

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializa el mapa
            let map;
            let marker;

            // Cuando el modal se muestra
            $('#modalMapa').on('shown.bs.modal', function(event) {
                const button = $(event.relatedTarget); // Botón que disparó el modal
                const direccion = button.data('direccion'); // Dirección de la entidad

                if (!map) {
                    map = L.map('map').setView([10.4806, -66.9036], 13); // Coordenadas iniciales

                    // Añade la capa de mosaicos de OpenStreetMap
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; Servi Aires'
                    }).addTo(map);
                }

                // Geocodifica la dirección usando Nominatim (API de OpenStreetMap)
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${direccion}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = data[0].lat;
                            const lon = data[0].lon;

                            // Centra el mapa y añade un marcador
                            map.setView([lat, lon], 15);
                            if (marker) {
                                marker.setLatLng([lat, lon]);
                            } else {
                                marker = L.marker([lat, lon]).addTo(map);
                            }
                        } else {
                            alert('No se pudo encontrar la ubicación');
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener la ubicación:', error);
                        alert('Error al obtener la ubicación');
                    });
            });

            // Limpia el mapa cuando el modal se cierra
            $('#modalMapa').on('hidden.bs.modal', function() {
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }
            });
        });
    </script>
@stop
