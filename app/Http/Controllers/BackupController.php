<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Storage::files('backups');
        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        $filename = 'Backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $mysqlPath = '"C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe"';
        $command = "{$mysqlPath} -u root mydb > " . storage_path("app/backups/{$filename}");
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return redirect()->route('backups.index')->with('error', 'Error al guardar la base de datos.');
        }
        return redirect()->route('backups.index')->with('success', 'Backup creado exitosamente.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string'
        ]);
        $filename = $request->input('backup_file');
        $mysqlPath = '"C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe"';
        $command = "{$mysqlPath} -u root mydb < " . storage_path("app/backups/{$filename}");
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return redirect()->route('backups.index')->with('error', 'Error al restaurar la base de datos.');
        }
        return redirect()->route('backups.index')->with('success', 'Base de datos restaurada exitosamente.');
    }

    public function download($file)
    {
        if (Storage::exists("backups/{$file}")) {
            return Storage::download("backups/{$file}");
        } else {
            return redirect()->route('backups.index')->with('error', 'El archivo no existe.');
        }
    }

    public function delete($file)
    {
        if (Storage::exists("backups/{$file}")) {
            Storage::delete("backups/{$file}");
            return redirect()->route('backups.index')->with('success', 'Backup eliminado exitosamente.');
        } else {
            return redirect()->route('backups.index')->with('error', 'El archivo no existe.');
        }
    }
}
