<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TechnicianAssignedNotification extends Notification
{
    use Queueable;

    protected $ticket;

    /**
     * Create a new notification instance.
     *
     * @param  Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nuevo Ticket Asignado')
            ->greeting('¡Hola, ' . $notifiable->nombreCompleto . '!')
            ->line('Se te ha asignado un nuevo ticket de reparación.')
            ->line('Detalles del ticket:')
            ->line('🔹 ID del Ticket: ' . $this->ticket->id_Tickets)
            ->line('🔹 Cliente: ' . $this->ticket->clienteRazonSocial->nombreCompleto)
            ->line('🔹 Equipo (N° Serie): ' . $this->ticket->propiedadEquipo->nroSerie)
            ->line('🔹 Estado: ' . $this->ticket->statusClasificacion->titulo)
            ->action('Ver Ticket', url(route('tickets.show', $this->ticket->id_Tickets)))
            ->line('Por favor, revisa el sistema y procede con la reparación.')
            ->salutation('Saludos, Servi Aires');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id_Tickets,
            'cliente' => $this->ticket->clienteRazonSocial->nombreCompleto,
            'equipo' => $this->ticket->propiedadEquipo->nroSerie,
            'estado' => $this->ticket->statusClasificacion->titulo,
        ];
    }
}
