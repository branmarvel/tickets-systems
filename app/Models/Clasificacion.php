<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    use HasFactory;

    protected $table = 'Clasificacion';
    protected $primaryKey = 'id_Clasificacion';
    protected $fillable = ['titulo', 'descripcion', 'icono', 'parent_id'];

    public function subclasificaciones()
    {
        return $this->hasMany(Clasificacion::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Clasificacion::class, 'parent_id');
    }
}
