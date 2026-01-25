<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IconController;
use App\Models\Clasificacion;
use App\Models\RazonSocial;
use App\Models\Equipo;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TechnicianAssignedNotification;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $statuses = Clasificacion::where('parent_id', '8')->pluck('titulo', 'id_Clasificacion');

        $query = Ticket::with('clienteRazonSocial', 'tecnicoAsignadoRazonSocial', 'propiedadEquipo', 'statusClasificacion');

        // Filtrar tickets según el rol del usuario
        $user = Auth::user();

        if ($user->rolClasificacion->titulo === 'Técnico') {
            $query->where('tecnAsignado', $user->id_RS);
        }

        $user = Auth::user();

        if ($user->rolClasificacion->titulo === 'Cliente') {
            $query->where('cliente', $user->id_RS);
        }

        // Filtrar por búsqueda si está presente
        if ($request->has('search') && $request->input('search') !== null) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_Tickets', 'like', '%' . $search . '%')
                    ->orWhereHas('clienteRazonSocial', function ($q) use ($search) {
                        $q->where('nombreCompleto', 'like', '%' . $search . '%')
                            ->orWhere('nroIdentificacion', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('tecnicoAsignadoRazonSocial', function ($q) use ($search) {
                        $q->where('nombreCompleto', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('propiedadEquipo', function ($q) use ($search) {
                        $q->where('nroSerie', 'like', '%' . $search . '%')
                            ->orWhere('descripcion', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('statusClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('status') && $request->input('status') !== null) {
            $query->where('status', $request->input('status'));
        }

        $tickets = $query->get();

        return view('tickets.index', compact('tickets', 'statuses'));
    }

    public function create()
    {
        $clientes = RazonSocial::pluck('nombreCompleto', 'id_RS');
        $tecnicos = RazonSocial::whereHas('rolClasificacion', function ($query) {
            $query->where('titulo', 'Tecnico');
        })
            ->withCount('tickets')
            ->orderBy('tickets_count')
            ->pluck('nombreCompleto', 'id_RS');
        $statuses = Clasificacion::where('parent_id', '8')->pluck('titulo', 'id_Clasificacion');
        return view('tickets.create', compact('clientes', 'tecnicos', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|exists:Clasificacion,id_Clasificacion',
            'fechaSolicitud' => 'required|date',
            'cliente' => 'required|exists:RazonSocial,id_RS',
            'equipo' => 'required|exists:Equipos,id_Equipos',
        ]);

        $tecnico = RazonSocial::whereHas('rolClasificacion', function ($query) {
            $query->where('titulo', 'Tecnico');
        })
            ->withCount('tickets')
            ->orderBy('tickets_count')
            ->first();

        if (!$tecnico) {
            return redirect()->route('tickets.index')
                ->with('error', 'No hay técnicos disponibles para asignar.');
        }

        $ticket = Ticket::create(array_merge($request->all(), ['tecnAsignado' => $tecnico->id_RS]));

        if (!empty($tecnico->email)) {
            $tecnico->notify(new TechnicianAssignedNotification($ticket));
        }

        return redirect()->route('tickets.index')
            ->with('Agregado', 'Ticket agregado exitosamente y notificación enviada al técnico.');
    }

    public function edit($id)
    {
        $ticket = Ticket::with('clienteRazonSocial', 'tecnicoAsignadoRazonSocial', 'propiedadEquipo')->findOrFail($id);
        $clientes = RazonSocial::pluck('nombreCompleto', 'id_RS');
        $tecnicos = RazonSocial::whereHas('rolClasificacion', function ($query) {
            $query->where('titulo', 'Tecnico');
        })->pluck('nombreCompleto', 'id_RS');
        $equipos = Equipo::all()->mapWithKeys(function ($equipo) {
            $info = $equipo->nroSerie . ' (' . $equipo->tipoEquipoClasificacion->titulo . ' - ' . $equipo->marcaClasificacion->titulo . ')';
            return [$equipo->id_Equipos => $info];
        });
        $statuses = Clasificacion::where('parent_id', '8')->pluck('titulo', 'id_Clasificacion');
        return view('tickets.edit', compact('ticket', 'clientes', 'tecnicos', 'equipos', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|exists:Clasificacion,id_Clasificacion',
            'fechaSolicitud' => 'required|date',
            'tecnAsignado' => 'required|exists:RazonSocial,id_RS',
            'cliente' => 'required|exists:RazonSocial,id_RS',
            'equipo' => 'required|exists:Equipos,id_Equipos',
        ]);

        $ticket = Ticket::findOrFail($id);

        $tecnicoAnteriorId = $ticket->tecnAsignado;

        $ticket->update($request->all());

        if ($tecnicoAnteriorId != $ticket->tecnAsignado) {
            $nuevoTecnico = RazonSocial::find($ticket->tecnAsignado);
            if ($nuevoTecnico && !empty($nuevoTecnico->email)) {
                $nuevoTecnico->notify(new TechnicianAssignedNotification($ticket));
            }
        }

        return redirect()->route('tickets.index')
            ->with('actualizar', 'Ticket actualizado exitosamente.');
    }

    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('tickets.show', compact('ticket'));
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado exitosamente.');
    }

    public function getEquiposPorCliente($idCliente)
    {
        $equipos = Equipo::where('id_RS', $idCliente)
            ->select('id_Equipos', 'nroSerie', 'tipoEquipo', 'marca')
            ->get()
            ->mapWithKeys(function ($equipo) {
                $info = $equipo->nroSerie . ' (' . $equipo->tipoEquipoClasificacion->titulo . ' - ' . $equipo->marcaClasificacion->titulo . ')';
                return [$equipo->id_Equipos => $info];
            });
        return response()->json($equipos);
    }

    public function getTickets()
    {
        $user = Auth::user();

        // Filtrar los tickets según el rol del usuario
        if ($user->rolClasificacion->titulo === 'Técnico') {
            $tickets = Ticket::where('tecnAsignado', $user->id_RS)->get();
        } else {
            // En caso de que no sea técnico, devolver todos o implementar lógica para otros roles
            $tickets = Ticket::all();
        }

        $events = collect();
        foreach ($tickets as $ticket) {
            $ticketEvents = [
                [
                    'id' => $ticket->id_Tickets,
                    'title' => 'Ticket #' . $ticket->id_Tickets . ': Solicitud',
                    'start' => $ticket->fechaSolicitud,
                    'end' => $ticket->fechaAsignacion,
                    'description' => 'Solicitud - ' . $ticket->statusClasificacion->titulo ?? 'N/A',
                    'cliente' => $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A',
                    'tecnico' => $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A',
                    'equipo' => $ticket->propiedadEquipo->nroSerie ?? 'N/A',
                    'type' => 'solicitud',
                ],
                [
                    'id' => $ticket->id_Tickets,
                    'title' => 'Ticket #' . $ticket->id_Tickets . ': Asignacion',
                    'start' => $ticket->fechaAsignacion,
                    'end' => $ticket->fechaDiagnostico,
                    'description' => 'Asignacion - ' . $ticket->statusClasificacion->titulo ?? 'N/A',
                    'cliente' => $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A',
                    'tecnico' => $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A',
                    'equipo' => $ticket->propiedadEquipo->nroSerie ?? 'N/A',
                    'type' => 'asignacion',
                ],
                [
                    'id' => $ticket->id_Tickets,
                    'title' => 'Ticket #' . $ticket->id_Tickets . ': Diagnostico',
                    'start' => $ticket->fechaDiagnostico,
                    'end' => $ticket->fechaAprobacion,
                    'description' => 'Diagnostico - ' . $ticket->statusClasificacion->titulo ?? 'N/A',
                    'cliente' => $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A',
                    'tecnico' => $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A',
                    'equipo' => $ticket->propiedadEquipo->nroSerie ?? 'N/A',
                    'type' => 'diagnostico',
                ],
                [
                    'id' => $ticket->id_Tickets,
                    'title' => 'Ticket #' . $ticket->id_Tickets . ': Aprobacion',
                    'start' => $ticket->fechaAprobacion,
                    'end' => $ticket->fechaReparacion,
                    'description' => 'Aprobacion - ' . $ticket->statusClasificacion->titulo ?? 'N/A',
                    'cliente' => $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A',
                    'tecnico' => $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A',
                    'equipo' => $ticket->propiedadEquipo->nroSerie ?? 'N/A',
                    'type' => 'aprobacion',
                ],
                [
                    'id' => $ticket->id_Tickets,
                    'title' => 'Ticket #' . $ticket->id_Tickets . ': Reparacion',
                    'start' => $ticket->fechaReparacion,
                    'end' => $ticket->fechaFinalizacion,
                    'description' => 'Reparacion - ' . $ticket->statusClasificacion->titulo ?? 'N/A',
                    'cliente' => $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A',
                    'tecnico' => $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A',
                    'equipo' => $ticket->propiedadEquipo->nroSerie ?? 'N/A',
                    'type' => 'reparacion',
                ],
                [
                    'id' => $ticket->id_Tickets,
                    'title' => 'Ticket #' . $ticket->id_Tickets . ': Finalizacion',
                    'start' => $ticket->fechaFinalizacion,
                    'end' => $ticket->fechaFinalizacion,
                    'description' => 'Finalizacion - ' . $ticket->statusClasificacion->titulo ?? 'N/A',
                    'cliente' => $ticket->clienteRazonSocial->nombreCompleto ?? 'N/A',
                    'tecnico' => $ticket->tecnicoAsignadoRazonSocial->nombreCompleto ?? 'N/A',
                    'equipo' => $ticket->propiedadEquipo->nroSerie ?? 'N/A',
                    'type' => 'finalizacion',
                ],
            ];
            $ticketEvents = array_filter($ticketEvents, function ($event) {
                return $event['start'] !== null;
            });
            $events = $events->merge($ticketEvents);
        }
        return response()->json($events);
    }

    public function updateEvent(Request $request)
    {
        $ticket = Ticket::find($request->id);
        switch ($request->type) {
            case 'solicitud':
                $ticket->fechaSolicitud = $request->start;
                $ticket->fechaAsignacion = $request->end;
                break;
            case 'asignacion':
                $ticket->fechaAsignacion = $request->start;
                $ticket->fechaDiagnostico = $request->end;
                break;
            case 'diagnostico':
                $ticket->fechaDiagnostico = $request->start;
                $ticket->fechaAprobacion = $request->end;
                break;
            case 'aprobacion':
                $ticket->fechaAprobacion = $request->start;
                $ticket->fechaReparacion = $request->end;
                break;
            case 'reparacion':
                $ticket->fechaReparacion = $request->start;
                $ticket->fechaFinalizacion = $request->end;
                break;
            case 'finalizacion':
                $ticket->fechaFinalizacion = $request->start;
                break;
        }
        $ticket->save();
        return response()->json(['status' => 'success', 'message' => 'Evento actualizado con éxito']);
    }
}
