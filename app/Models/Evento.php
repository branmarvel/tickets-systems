<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'Eventos';
    protected $primaryKey = 'id_Eventos';
    protected $fillable = [
        'usuario',
        'tipoEvento',
        'descripcion',
        'archivo',
        'fechaEvento',
        'fechaInicio',
        'fechaFin',
        'costo',
        'ticket_id',
    ];
    protected $dates = [
        'fechaEvento',
        'fechaInicio',
        'fechaFin',
    ];

    public function usuarioRazonSocial()
    {
        return $this->belongsTo(RazonSocial::class, 'usuario', 'id_RS');
    }

    public function tipoEventoClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'tipoEvento', 'id_Clasificacion');
    }

    public function ticketTickets()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
