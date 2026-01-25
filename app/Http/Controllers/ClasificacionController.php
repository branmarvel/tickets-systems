<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clasificacion;
use App\Http\Controllers\IconController;

class ClasificacionController extends Controller
{
    public function index()
    {
        $clasificaciones = Clasificacion::whereNull('parent_id')->get();
        return view('clasificaciones.index', compact('clasificaciones'));
    }

    public function create()
    {
        $iconController = new IconController();
        $icons = $iconController->getIcons()->original;
        return view('clasificaciones.create', compact('icons'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'titulo' => 'required|string',
            'descripcion' => 'nullable|string',
            'icono' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);
        $clasificacion = new Clasificacion($request->all());
        if (is_null($clasificacion->parent_id)) {
            $lastId = Clasificacion::whereNull('parent_id')->max('id_Clasificacion') ?? 0;
            $clasificacion->id_Clasificacion = ($lastId < 10000) ? $lastId + 1 : 1;
        }
        $clasificacion->save();
        return redirect()->route('clasificaciones.index')->with('agregado', 'La clasificación se a agregado exitosamente.');
    }

    public function edit($id)
    {
        $clasificacion = Clasificacion::findOrFail($id);
        $iconController = new IconController();
        $icons = $iconController->getIcons()->original;
        return view('clasificaciones.edit', compact('clasificacion', 'icons'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $clasificacion = Clasificacion::findOrFail($id);
        $clasificacion->update($request->all());
        return redirect()->route('clasificaciones.index')->with('actualizar', 'La clasificación se a actualizado exitosamente.');
    }

    public function destroy($id)
    {
        Clasificacion::destroy($id);
        return redirect()->route('clasificaciones.index')->with('success', 'La clasificación ha sido eliminada.');
    }
}
