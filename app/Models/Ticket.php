<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'Tickets';
    protected $primaryKey = 'id_Tickets';
    protected $fillable = [
        'status',
        'fechaSolicitud',
        'fechaAsignacion',
        'fechaDiagnostico',
        'fechaAprobacion',
        'fechaReparacion',
        'fechaFinalizacion',
        'tecnAsignado',
        'cliente',
        'equipo',
    ];
    protected $dates = [
        'fechaSolicitud',
        'fechaAsignacion',
        'fechaDiagnostico',
        'fechaAprobacion',
        'fechaReparacion',
        'fechaFinalizacion',
    ];

    public function clienteRazonSocial()
    {
        return $this->belongsTo(RazonSocial::class, 'cliente', 'id_RS');
    }

    public function tecnicoAsignadoRazonSocial()
    {
        return $this->belongsTo(RazonSocial::class, 'tecnAsignado', 'id_RS');
    }

    public function statusClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'status', 'id_Clasificacion');
    }

    public function propiedadEquipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo', 'id_Equipos');
    }

    public function eventosEvento()
    {
        return $this->hasMany(Evento::class, 'ticket_id');
    }
}
