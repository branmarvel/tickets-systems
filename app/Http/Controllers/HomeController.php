<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Ticket;
use App\Models\Evento;
use App\Models\RazonSocial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
{
    $user = Auth::user();
    
    // Inicializamos la variable countRazonSocial por defecto
    $countRazonSocial = RazonSocial::count();
    
    // Verificar el rol del usuario
    if ($user->rolClasificacion->titulo === 'Técnico') {
        // Obtener solo los tickets asignados al técnico
        $tickets = Ticket::where('tecnAsignado', $user->id_RS)->get();
        $ticketIds = $tickets->pluck('id_Tickets'); // Extraer los IDs de los tickets

        // Contadores filtrados solo para el técnico
        $countEquipos = Ticket::where('status', 10084)
            ->where('tecnAsignado', $user->id_RS)
            ->count();
        $countTickets = $tickets->count();

        // Contar eventos asociados a los tickets del técnico
        $countEventos = Evento::whereIn('ticket_id', $ticketIds)->count();

        // Obtener el conteo de tickets por estatus solo para el técnico
        $statuses = [10080, 10081, 10082, 10083, 10084, 10085, 10086];
        $statusCounts = [];

        foreach ($statuses as $status) {
            $statusCounts[$status] = Ticket::where('status', $status)
                ->where('tecnAsignado', $user->id_RS)
                ->count();
        }
    } else if ($user->rolClasificacion->titulo === 'Operador') {
        // Si el usuario es operador, solo contar los clientes
        $countRazonSocial = RazonSocial::where('rol', 10007)->count();  // Solo clientes
        $countEquipos = Equipo::count();
        $countTickets = Ticket::count();
        $countEventos = Evento::count(); // Todos los eventos

        // Obtener el conteo de tickets por estatus globalmente
        $statuses = [10080, 10081, 10082, 10083, 10084, 10085, 10086];
        $statusCounts = [];

        foreach ($statuses as $status) {
            $statusCounts[$status] = Ticket::where('status', $status)->count();
        }

        // No obtener los tickets ni eventos asignados a un técnico, solo global
        $tickets = Ticket::all();
    } else {
        // Si el usuario no es técnico ni operador, obtener todos los tickets y eventos
        $tickets = Ticket::all();
        $countEquipos = Equipo::count();
        $countTickets = Ticket::count();
        $countEventos = Evento::count(); // Todos los eventos

        // Obtener el conteo de tickets por estatus globalmente
        $statuses = [10080, 10081, 10082, 10083, 10084, 10085, 10086];
        $statusCounts = [];

        foreach ($statuses as $status) {
            $statusCounts[$status] = Ticket::where('status', $status)->count();
        }
    }

    return view('home', compact('countEquipos', 'countTickets', 'countEventos', 'countRazonSocial', 'user', 'statusCounts', 'tickets'));
}

}
