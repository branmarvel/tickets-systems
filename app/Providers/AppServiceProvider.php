<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Policies\TicketPolicy;
use App\Models\Clasificacion;
use App\Models\Ticket;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::resourceParameters([
            'clasificaciones' => 'clasificacion',
            'equipos' => 'equipo'
        ]);

        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $user = Auth::user();

            // Si no hay un usuario autenticado, no construir el menú
            if (!$user) {
                return;
            }

            // Obtener todas las clasificaciones principales
            $clasificaciones = Clasificacion::whereNull('parent_id')->get();

            // Crear el submenú de clasificaciones
            $clasificacionesSubmenu = [];
            foreach ($clasificaciones as $clasificacion) {
                $clasificacionesSubmenu[] = [
                    'key'  => 'clasificacion_' . $clasificacion->id_Clasificacion,
                    'text' => $clasificacion->titulo,
                    'url'  => route('subclasificaciones.index', $clasificacion->id_Clasificacion),
                    'icon' => 'fas ' . $clasificacion->icono,
                ];
            }

            $ticketStatuses = Clasificacion::where('parent_id', '8')->get(); // Ajusta el 'parent_id' según corresponda

            // Crear el submenú de clasificaciones
            $statusesSubmenu = [];
            foreach ($ticketStatuses as $status) {
                if (in_array($user->rol, [10004, 10006])) {
                    // Administrador y operador: ven el conteo total de tickets por estatus
                    $count = Ticket::where('status', $status->id_Clasificacion)->count();
                } elseif ($user->rol == 10007) {
                    // Cliente: ve solo el conteo de tickets que él ha creado
                    $count = Ticket::where('status', $status->id_Clasificacion)
                        ->where('cliente', $user->id_RS) // Filtrar por el cliente
                        ->count();
                } else {
                    // Otros roles: ven solo el conteo de tickets asignados a ellos
                    $count = Ticket::where('status', $status->id_Clasificacion)
                        ->where('tecnAsignado', $user->id_RS) // Campo de asignación
                        ->count();
                }

                $statusesSubmenu[] = [
                    'key'   => 'status_' . $status->id_Clasificacion,
                    'text'  => $status->titulo,
                    'url'   => route('tickets.index', ['status' => $status->id_Clasificacion]),
                    'icon'  => 'fas ' . $status->icono,
                    'label' => $count,
                ];
            }

            if (in_array($user->rol, [10004, 10005, 10006])) {
                $event->menu->add([
                    'text' => 'Dashboard',
                    'url' => '/dashboard',
                    'icon' => 'fas fa-home',
                ]);
            }
        
            // Construir el menú principal basado en roles
            $menuItems = [
                [
                    'text' => 'Panel Administrativo',
                    'icon' => 'fas fa-fw fa-share',
                    'submenu' => [],
                ],
            ];

            // Verificar si el usuario tiene acceso al menú de Copia de Seguridad
            if (in_array($user->rol, [10004])) { // Solo visible para administradores
                $menuItems[] = [
                    'text' => 'Copia de Seguridad',
                    'url' => '/backups',
                    'icon' => 'fas fa-hdd',
                ];
            }

            // Opciones para Administrador (rol: 10004)
            if (in_array($user->rol, [10004])) {
                $menuItems[0]['submenu'][] = [
                    'text' => 'Clasificaciones',
                    'icon' => 'fas fa-star',
                    'submenu' => array_merge([
                        [
                            'text' => 'Ver Todas',
                            'url' => route('clasificaciones.index'),
                            'icon' => 'fas fa-list',
                        ]
                    ], $clasificacionesSubmenu),
                ];
            }

            // Opciones para Administrador y Operador (roles: 10004, 10006)
            if (in_array($user->rol, [10004, 10006])) {
                $menuItems[0]['submenu'][] = [
                    'text' => 'Entidades',
                    'url' => '/razon_social',
                    'icon' => 'fas fa-user-plus',
                ];
                $menuItems[0]['submenu'][] = [
                    'text' => 'Equipos',
                    'url' => '/equipos',
                    'icon' => 'fas fa-boxes',
                ];
            }

            // Opciones para todos los roles (roles: 10004, 10005, 10006, 10007)
            if (in_array($user->rol, [10004, 10005, 10006, 10007])) {
                $menuItems[0]['submenu'][] = [
                    'text' => 'Tickets',
                    'icon' => 'fas fa-ticket-alt',
                    'submenu' => array_merge([
                        [
                            'text' => 'Ver Todos',
                            'url' => route('tickets.index'),
                            'icon' => 'fas fa-list',
                        ]
                    ], $statusesSubmenu),
                ];

                $menuItems[0]['submenu'][] = [
                    'text' => 'Actividades',
                    'url' => '/eventos',
                    'icon' => 'fas fa-tasks',
                ];
            }

            // Añadir los elementos al menú
            foreach ($menuItems as $menuItem) {
                $event->menu->add($menuItem);
            }
        });
    }
}
