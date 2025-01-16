<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrafoController extends Controller
{
    public function index() {
        $user_id = Auth::id(); // Obtener el ID del usuario autenticado

        // Recuperar los grafos del usuario desde la base de datos
        $grafos = DB::table('grafos')->where('user_id', $user_id)->get();

        return view('dashboard', ['grafos' => $grafos]);
    }

    public function create() {
        // Muestra la vista para generar un nuevo grafo
        return view('generar-grafo');
    }

    public function store(Request $request) {
        // Lógica para generar un grafo a partir de la consulta
        $query = $request->input('query');
        $user_id = Auth::id(); // Obtener el ID del usuario autenticado

        // Ruta relativa al entorno virtual y al script de Python
        $venv_path = '/home/javier/Documentos/EntornoPLN/bin/activate'; // Ruta absoluta al entorno virtual
        $script_path = base_path('python/noticias.py'); // Ruta relativa al script de Python

        // Comando para activar el entorno virtual y ejecutar el script de Python
        $command = "source $venv_path && python3 $script_path " . escapeshellarg(env('NEWSAPI_KEY')) . " " . escapeshellarg($query) . " " . escapeshellarg(env('NEO4J_URI')) . " " . escapeshellarg(env('NEO4J_USER')) . " " . escapeshellarg(env('NEO4J_PASSWORD')) . " " . escapeshellarg($user_id);

        // Ejecución del comando
        $output = shell_exec($command);

        // Guardar información del grafo en la base de datos
        DB::table('grafos')->insert([
            'user_id' => $user_id,
            'query' => $query,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirigir al dashboard
        return redirect()->route('dashboard');
    }
}
