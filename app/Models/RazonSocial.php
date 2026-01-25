<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class RazonSocial extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, HasFactory, CanResetPasswordTrait, Notifiable;


    protected $table = 'RazonSocial';
    protected $primaryKey = 'id_RS';
    protected $fillable = [
        'nombreCompleto',
        'tipoPersona',
        'nroIdentificacion',
        'telefono',
        'email',
        'direccion',
        'rol',
        'contraseña'
    ];

    public function tipoPersonaClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'tipoPersona', 'id_Clasificacion');
    }

    public function rolClasificacion()
    {
        return $this->belongsTo(Clasificacion::class, 'rol', 'id_Clasificacion');
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'id_RS', 'id_RS');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'tecnAsignado', 'id_RS');
    }

    public function ticketsCount()
    {
        return $this->tickets()->count();
    }

    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    // Métodos requeridos por AdminLTE
    public function adminlte_image()
    {
        // Devuelve el ícono asociado al rol como clase CSS si es un ícono de FontAwesome
        if ($this->rolClasificacion && $this->rolClasificacion->icono) {
            return $this->rolClasificacion->icono;
        }
        return 'fas fa-user'; // Ícono por defecto
    }

    public function adminlte_desc()
    {
        return $this->rolClasificacion ? $this->rolClasificacion->titulo : 'Sin rol';
    }

    public function adminlte_profile_url()
    {
        return null; // Puedes devolver la URL del perfil del usuario aquí si tienes una página de perfil
    }

    public function adminlte_name()
    {
        return $this->nombreCompleto; // Devuelve el nombre completo del usuario
    }

    public function sendPasswordResetNotification($token)
{
    $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
}
    
}
