<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'Equipos';
    protected $primaryKey = 'id_Equipos';
    protected $fillable = ['descripcion', 'marca', 'modelo', 'nroSerie', 'linea', 'tipoEquipo', 'precio', 'motivo', 'id_RS',];

    public function marcaClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'marca', 'id_Clasificacion');
    }

    public function lineaClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'linea', 'id_Clasificacion');
    }

    public function tipoEquipoClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'tipoEquipo', 'id_Clasificacion');
    }

    public function motivoClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'motivo', 'id_Clasificacion');
    }

    public function dueñoRazonSocial()
    {
        return $this->belongsTo(RazonSocial::class, 'id_RS', 'id_RS');
    }
}
