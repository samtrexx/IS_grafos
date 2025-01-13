<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GrafoController extends Controller
{
    
    public function index() {
        // Lógica para mostrar grafos previos en el dashboard
        return view('dashboard');
    }

    public function create() {
        // Muestra la vista para generar un nuevo grafo
        return view('generar-grafo');
    }

    public function store(Request $request) {
        // Lógica para generar un grafo a partir de la consulta
        $query = $request->input('query');

        // Ruta relativa al entorno virtual y al script de Python
        $venv_path = '/home/javier/Documentos/EntornoPLN/bin/activate'; // Ruta absoluta al entorno virtual
        $script_path = base_path('python/noticias.py'); // Ruta relativa al script de Python

        // Comando para activar el entorno virtual y ejecutar el script de Python
        $command = "source $venv_path && python3 $script_path " . escapeshellarg(env('NEWSAPI_KEY')) . " " . escapeshellarg($query) . " " . escapeshellarg(env('NEO4J_URI')) . " " . escapeshellarg(env('NEO4J_USER')) . " " . escapeshellarg(env('NEO4J_PASSWORD'));

        // Ejecución del comando
        $output = shell_exec($command);

        // Redirigir al dashboard
        return redirect()->route('dashboard');
    }
}
