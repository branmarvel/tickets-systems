<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IconController;
use App\Models\Evento;
use App\Models\RazonSocial;
use App\Models\Clasificacion;
use App\Models\Ticket;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class EventoController extends Controller
{

    public function index(Request $request)
{
    $user = Auth::user();
    $ticket_id = $request->input('ticket_id');

    // Filtrar tickets según el rol del usuario
    if ($user->rol == 10007) { // Cliente
        $tickets = Ticket::where('cliente', $user->id_RS)->get();
    } elseif ($user->rol == 10005) { // Técnico
        $tickets = Ticket::where('tecnAsignado', $user->id_RS)->get(); // Filtra por el técnico asignado
    } else { // Otros roles ven todos los tickets
        $tickets = Ticket::all();
    }

    // Si no hay tickets disponibles, mostrar solo la lista vacía
    if ($tickets->isEmpty()) {
        return view('eventos.index', compact('tickets'));
    }

    // Si no se ha seleccionado un ticket, solo mostrar la lista de tickets
    if (!$ticket_id) {
        return view('eventos.index', compact('tickets'));
    }

    // Buscar el ticket asegurando que pertenece al usuario si es cliente o técnico
    $ticket = Ticket::find($ticket_id);

    if (!$ticket || ($user->rol == 10007 && $ticket->cliente != $user->id_RS)) {
        abort(403, 'No tienes permiso para ver este ticket.');
    }

    // Construir la consulta de eventos
    $query = Evento::where('ticket_id', $ticket_id);

    if ($user->rol == 10007) {
        $query->where('tipoEvento', 10095); // Tipo de evento para el cliente
    } // Ya no es necesario un filtro para los técnicos
    
    // Obtener los eventos filtrados
    $eventos = $query->with('usuarioRazonSocial', 'tipoEventoClasificacion')->get();

    return view('eventos.index', compact('eventos', 'tickets', 'ticket_id', 'ticket'));
}



    public function convertVEStoUSD(Request $request)
    {
        // Obtiene el valor del request
        $value = $request->input('value');

        // Validar que el valor sea numérico y mayor que cero
        if (!$value || !is_numeric($value) || $value <= 0) {
            return response()->json(['error' => 'Valor inválido'], 400);
        }

        // Llamada a la API
        try {
            $response = Http::get("https://pydolarve.org/api/v1/dollar/conversion?type=VES&value={$value}&page=bcv&monitor=usd");

            // Verificar si la respuesta es exitosa
            if ($response->successful()) {
                // La API ahora devuelve el valor directo
                $usdValue = $response->body(); // Obtiene el valor directo como string

                // Formatear el valor a 3 decimales
                $formattedValue = number_format(floatval($usdValue), 3, ',', '');

                // Retorna el valor como JSON
                return response()->json(['usd' => $formattedValue]);
            } else {
                return response()->json(['error' => 'Error en la API'], 500);
            }
        } catch (\Exception $e) {
            // Manejo de excepciones
            return response()->json(['error' => 'Error en la conversión: ' . $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $ticket_id = $request->input('ticket_id');
        $ticket = Ticket::findOrFail($ticket_id);
        $usuarios = RazonSocial::all();
        $currentUser = Auth::user();
        $tiposEvento = Clasificacion::where('parent_id', '7')->get();
        return view('eventos.create', compact('usuarios', 'currentUser', 'tiposEvento', 'ticket_id'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'usuario' => 'required|exists:RazonSocial,id_RS',
            'tipoEvento' => 'required|exists:Clasificacion,id_Clasificacion',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|max:204800',
            'fechaEvento' => 'required|date',
            'fechaInicio' => 'nullable|date',
            'fechaFin' => 'nullable|date',
            'costo' => 'nullable|numeric',
            'ticket_id' => 'required|exists:Tickets,id_Tickets',
        ]);
        $evento = new Evento($request->except('archivo'));
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('archivos_eventos', 'public');
            $evento->archivo = $archivoPath;
        }
        $evento->save();
        return redirect()->route('eventos.index', ['ticket_id' => $request->ticket_id])->with('agregado', 'Actividad agregado exitosamente.');
    }

    public function edit($id)
    {
        $evento = Evento::findOrFail($id);
        $ticket_id = $evento->ticket_id;
        $usuarios = RazonSocial::all();
        $currentUser = Auth::user();
        $tiposEvento = Clasificacion::where('parent_id', '7')->get();
        return view('eventos.edit', compact('evento', 'ticket_id', 'usuarios', 'currentUser', 'tiposEvento'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'usuario' => 'required|exists:RazonSocial,id_RS',
            'tipoEvento' => 'required|exists:Clasificacion,id_Clasificacion',
            'descripcion' => 'nullable|string',
            'archivo' => 'nullable|file|max:204800',
            'fechaEvento' => 'required|date',
            'fechaInicio' => 'nullable|date',
            'fechaFin' => 'nullable|date',
            'costo' => 'nullable|numeric',
        ]);
        $evento = Evento::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        if ($request->hasFile('archivo')) {
            if ($evento->archivo && Storage::disk('public')->exists($evento->archivo)) {
                Storage::disk('public')->delete($evento->archivo);
            }
            $archivoPath = $request->file('archivo')->store('archivos_eventos', 'public');
            $data['archivo'] = $archivoPath;
        }
        $evento->update($data);
        return redirect()->route('eventos.index', ['ticket_id' => $evento->ticket_id])->with('actualizar', 'Actividad actualizado exitosamente.');
    }

    public function show($id)
    {
        $evento = Evento::findOrFail($id);
        $ticket_id = $evento->ticket_id;
        try {
            $response = Http::get("https://pydolarve.org/api/v1/dollar/conversion?type=VES&value={$evento->costo}&page=bcv&monitor=usd");
            if ($response->successful()) {
                $usdValue = round(floatval($response->body()), 3);
            } else {
                $usdValue = 'N/A';
            }
        } catch (\Exception $e) {
            $usdValue = 'N/A';
        }
        return view('eventos.show', compact('evento', 'ticket_id', 'usdValue'));
    }

    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $ticket_id = $evento->ticket_id;
        if ($evento->archivo && Storage::disk('public')->exists($evento->archivo)) {
            Storage::disk('public')->delete($evento->archivo);
        }
        $evento->delete();
        return redirect()->route('eventos.index', ['ticket_id' => $ticket_id])->with('success', 'Actividad eliminado exitosamente.');
    }
}
