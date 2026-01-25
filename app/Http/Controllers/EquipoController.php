<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IconController;
use App\Models\Equipo;
use App\Models\Clasificacion;
use App\Models\RazonSocial;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipo::with('marcaClasificacion', 'lineaClasificacion', 'tipoEquipoClasificacion', 'motivoClasificacion', 'dueñoRazonSocial');
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', '%' . $search . '%')
                    ->orWhere('nroSerie', 'like', '%' . $search . '%')
                    ->orWhere('modelo', 'like', '%' . $search . '%')
                    ->orWhereHas('marcaClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('lineaClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('tipoEquipoClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('motivoClasificacion', function ($q) use ($search) {
                        $q->where('titulo', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('dueñoRazonSocial', function ($q) use ($search) {
                        $q->where('nombreCompleto', 'like', '%' . $search . '%')
                            ->orWhere('nroIdentificacion', 'like', '%' . $search . '%');
                    });
            });
        }
        $equipos = $query->get();
        return view('equipos.index', compact('equipos'));
    }

    public function create()
    {
        $marcas = Clasificacion::where('parent_id', '3')->pluck('titulo', 'id_Clasificacion');
        $lineas = Clasificacion::where('parent_id', '4')->pluck('titulo', 'id_Clasificacion');
        $tipoEquipo = Clasificacion::where('parent_id', '5')->pluck('titulo', 'id_Clasificacion');
        $motivos = Clasificacion::where('parent_id', '6')->pluck('titulo', 'id_Clasificacion');
        $dueño = RazonSocial::pluck('nombreCompleto', 'id_RS');
        return view('equipos.create', compact('marcas', 'lineas', 'tipoEquipo', 'motivos', 'dueño'));
    }

    public function store(Request $request)
    {
        // dd($validated->all());
        $validated = $request->validate([
            'descripcion' => 'nullable|string',
            'marca' => 'required|exists:Clasificacion,id_Clasificacion',
            'modelo' => 'nullable|string',
            'nroSerie' => 'required|string',
            'linea' => 'required|exists:Clasificacion,id_Clasificacion',
            'tipoEquipo' => 'required|exists:Clasificacion,id_Clasificacion',
            'precio' => 'nullable|numeric',
            'motivo' => 'required|exists:Clasificacion,id_Clasificacion',
            'id_RS' => 'nullable|exists:RazonSocial,id_RS',
        ]);
        Equipo::create($validated);
        return redirect()->route('equipos.index')->with('agregado', 'El Equipo se a agregado exitosamente.');
    }

    public function edit($id)
    {
        $equipo = Equipo::findOrFail($id);
        $marcas = Clasificacion::where('parent_id', '3')->pluck('titulo', 'id_Clasificacion');
        $lineas = Clasificacion::where('parent_id', '4')->pluck('titulo', 'id_Clasificacion');
        $tipoEquipo = Clasificacion::where('parent_id', '5')->pluck('titulo', 'id_Clasificacion');
        $motivos = Clasificacion::where('parent_id', '6')->pluck('titulo', 'id_Clasificacion');
        $dueño = RazonSocial::pluck('nombreCompleto', 'id_RS');
        return view('equipos.edit', compact('equipo', 'marcas', 'lineas', 'tipoEquipo', 'motivos', 'dueño'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        // dd($validated->all());
        $validated = $request->validate([
            'descripcion' => 'nullable|string',
            'marca' => 'required|exists:Clasificacion,id_Clasificacion',
            'modelo' => 'nullable|string',
            'nroSerie' => 'required|string',
            'linea' => 'required|exists:Clasificacion,id_Clasificacion',
            'tipoEquipo' => 'required|exists:Clasificacion,id_Clasificacion',
            'precio' => 'nullable|numeric',
            'motivo' => 'required|exists:Clasificacion,id_Clasificacion',
            'id_RS' => 'nullable|exists:RazonSocial,id_RS',
        ]);
        $equipo->update($validated);
        return redirect()->route('equipos.index')->with('actualizar', 'El Equipo se a actualizado exitosamente.');
    }

    public function show(Equipo $equipo)
    {
        return view('equipos.show', compact('equipo'));
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete();
        return redirect()->route('equipos.index')->with('success', 'El equipo ha sido eliminado.');
    }

    public function getClient(Request $request)
    {
        $nroIdentificacion = $request->input('nroIdentificacion');
        $dueño = RazonSocial::where('nroIdentificacion', $nroIdentificacion)->first();
        if ($dueño) {
            return response()->json([
                'id_RS' => $dueño->id_RS,
                'nombreCompleto' => $dueño->nombreCompleto,
            ]);
        }
        return response()->json([
            'id_RS' => null,
            'nombreCompleto' => 'Cliente no encontrada o no válida',
        ]);
    }
}
