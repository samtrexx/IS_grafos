<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Neo4jService;

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

    public function store(Request $request, Neo4jService $neo4jService)
    {
        $query = $request->input('query');
        $user_id = Auth::id();

        try {
            // Ejecutar consulta en Neo4j
            $cypherQuery = "
            CREATE (n:Grafo {query: \$query, user_id: \$user_id, created_at: timestamp()})
            RETURN n
        ";
            $neo4jService->runQuery($cypherQuery, [
                'query' => $query,
                'user_id' => $user_id,
            ]);

            // Guardar en la base de datos
            DB::table('grafos')->insert([
                'user_id' => $user_id,
                'query' => $query,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Grafo generado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Ocurrió un error al generar el grafo: ' . $e->getMessage());
        }
    }

    public function getGraphData(Neo4jService $neo4jService)
    {
        try {
            // Consulta para obtener nodos y relaciones de Neo4j
            $cypherQuery = "MATCH (n)-[r]->(m) RETURN n, r, m LIMIT 100";
            $result = $neo4jService->runQuery($cypherQuery);

            // Formatear los nodos y las relaciones
            $nodes = [];
            $edges = [];

            foreach ($result as $record) {
                // Formatear los nodos
                $nodes[] = [
                    'id' => $record->get('n')->value('id'),
                    'label' => $record->get('n')->value('name'),
                ];

                // Formatear las relaciones
                $edges[] = [
                    'from' => $record->get('n')->value('id'),
                    'to' => $record->get('m')->value('id'),
                    'label' => $record->get('r')->type(),
                ];
            }

            // Retornar los nodos y relaciones como respuesta JSON
            return response()->json([
                'nodes' => $nodes,
                'edges' => $edges,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }


}
