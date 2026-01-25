<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IconController;
use App\Models\RazonSocial;
use App\Models\Clasificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RazonSocialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = RazonSocial::with('tipoPersonaClasificacion', 'rolClasificacion');

        // Filtrar si el usuario es "Operador"
        if ($user->rolClasificacion->titulo === 'Operador') {
            $query->where('rol', 'like', 10007);  // Asumimos que el rol "Operador" solo ve clientes
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombreCompleto', 'like', '%' . $search . '%')
                    ->orWhere('nroIdentificacion', 'like', '%' . $search . '%')
                    ->orWhere('telefono', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhereHas('tipoPersonaClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('rolClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    });
            });
        }

        $razonSocials = $query->get();
        return view('razon_social.index', compact('razonSocials'));
    }


    public function create()
    {
        $tipoPersonas = Clasificacion::where('parent_id', '1')->pluck('titulo', 'id_Clasificacion');
        $roles = Clasificacion::where('parent_id', '2')->pluck('titulo', 'id_Clasificacion');
        return view('razon_social.create', compact('tipoPersonas', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreCompleto' => 'required|string',
            'tipoPersona' => 'required|exists:Clasificacion,id_Clasificacion',
            'nroIdentificacion' => 'required|string|unique:razonsocial,nroIdentificacion',
            'telefono' => 'required|string',
            'email' => 'nullable|string',
            'direccion' => 'nullable|string',
            'rol' => 'required|exists:Clasificacion,id_Clasificacion',
            'contraseña' => 'nullable|string|confirmed',
        ], [
            'nroIdentificacion.unique' => 'El número de identificación ya está en uso.',
            'contraseña.confirmed' => 'Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.',
        ]);

        if ($validated['contraseña']) {
            $validated['contraseña'] = bcrypt($validated['contraseña']);
        } else {
            unset($validated['contraseña']);
        }

        // Se usa el método estático create() correctamente
        RazonSocial::create($validated);

        return redirect()->route('razon_social.index')->with('agregado', 'La entidad fue agregada exitosamente.');
    }
    
    public function edit($id_RS)
    {
        $razonSocial = RazonSocial::findOrFail($id_RS);
        $tipoPersonas = Clasificacion::where('parent_id', '1')->pluck('titulo', 'id_Clasificacion');
        $roles = Clasificacion::where('parent_id', '2')->pluck('titulo', 'id_Clasificacion');
        return view('razon_social.edit', compact('razonSocial', 'tipoPersonas', 'roles'));
    }

    public function update(Request $request, $id_RS)
    {
        $razonSocial = RazonSocial::findOrFail($id_RS);

        $validated = $request->validate([
            'nombreCompleto' => 'required|string',
            'tipoPersona' => 'required|exists:Clasificacion,id_Clasificacion',
            'nroIdentificacion' => 'required|string|unique:razonsocial,nroIdentificacion,' . $id_RS . ',id_RS',
            'telefono' => 'required|string',
            'email' => 'nullable|string',
            'direccion' => 'nullable|string',
            'rol' => 'required|exists:Clasificacion,id_Clasificacion',
            'contraseña' => 'nullable|string|confirmed',
        ], [
            'nroIdentificacion.unique' => 'El número de identificación ya está en uso.',
            'contraseña.confirmed' => 'Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.',
        ]);

        // Solo se encripta la contraseña si se proporciona una nueva
        if ($request->filled('contraseña')) {
            $validated['contraseña'] = bcrypt($validated['contraseña']);
        } else {
            unset($validated['contraseña']);
        }

        $razonSocial->update($validated);

        return redirect()->route('razon_social.index')->with('actualizar', 'La entidad fue actualizada exitosamente.');
    }

    public function show(RazonSocial $razonSocial)
    {
        return view('razon_social.show', compact('razonSocial'));
    }

    public function destroy(RazonSocial $razonSocial)
    {
        $razonSocial->delete();
        return redirect()->route('razon_social.index')->with('success', 'La entidad fue eliminada exitosamente.');
    }
}
