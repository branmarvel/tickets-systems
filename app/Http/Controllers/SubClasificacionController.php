<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IconController;
use App\Models\Clasificacion;
use Illuminate\Http\Request;

class SubClasificacionController extends Controller
{
    public function index($parentId)
    {
        $clasificacion = Clasificacion::findOrFail($parentId);
        $subclasificaciones = Clasificacion::where('parent_id', $parentId)->get();
        return view('subclasificaciones.index', compact('clasificacion', 'subclasificaciones'));
    }

    public function create($parentId)
    {
        $clasificacion = Clasificacion::findOrFail($parentId);
        $iconController = new IconController();
        $icons = $iconController->getIcons()->original;
        return view('subclasificaciones.create', compact('clasificacion'));
    }

    public function store(Request $request, $parentId)
    {
        // dd($request->all());
        $subclasificacion = new Clasificacion($request->all());
        $lastId = Clasificacion::whereNotNull('parent_id')->max('id_Clasificacion') ?? 10000;
        $subclasificacion->id_Clasificacion = $lastId + 1;
        $subclasificacion->parent_id = $parentId;
        $subclasificacion->save();
        return redirect()->route('subclasificaciones.index', $parentId)->with('agregado', 'La subclasificación se a agregado exitosamente.');
    }

    public function edit($parentId, $id)
    {
        $clasificacion = Clasificacion::findOrFail($parentId);
        $subclasificacion = Clasificacion::findOrFail($id);
        $iconController = new IconController();
        $icons = $iconController->getIcons()->original;
        return view('subclasificaciones.edit', compact('clasificacion', 'subclasificacion'));
    }

    public function update(Request $request, $parentId, $id)
    {
        // dd($request->all());
        $subclasificacion = Clasificacion::findOrFail($id);
        $clasificacion = Clasificacion::findOrFail($parentId);
        $request->merge(['parentId' => $clasificacion->parentId]);
        $subclasificacion->update($request->all());
        return redirect()->route('subclasificaciones.index', $parentId)->with('actualizar', 'La subclasificación se a actualizado exitosamente.');
    }

    public function destroy($parentId, $id)
    {
        Clasificacion::destroy($id);
        return redirect()->route('subclasificaciones.index', $parentId)->with('success', 'La subclasificación ha sido eliminada.');
    }
}
