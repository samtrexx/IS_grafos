<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Neo4jService;

class GrafoController extends Controller
{
    protected $neo4jService;

    public function __construct(Neo4jService $neo4jService)
    {
        $this->neo4jService = $neo4jService;
    }

    public function store(Request $request)
    {
        $query = $request->input('query');
        $user_id = Auth::id();

        try {
            // Crear el nodo en Neo4j
            $cypherQuery = "
            CREATE (n:Grafo {query: \$query, user_id: \$user_id, created_at: datetime()})
            RETURN n
            ";
            $this->neo4jService->runQuery($cypherQuery, [
                'query' => $query,
                'user_id' => $user_id,
            ]);

            // Guardar el grafo en la base de datos local
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

    public function getGraphData()
    {
        try {
            // Consulta para obtener nodos y relaciones
            $cypherQuery = "MATCH (n)-[r]->(m) RETURN n, r, m LIMIT 100";
            $result = $this->neo4jService->runQuery($cypherQuery);

            // Formatear los nodos y relaciones
            $nodes = [];
            $edges = [];

            foreach ($result as $record) {
                $nodeN = $record['data'][0];
                $nodeM = $record['data'][1];
                $relation = $record['data'][2];

                $nodes[] = [
                    'id' => $nodeN['id'],
                    'label' => $nodeN['properties']['name'] ?? 'Unnamed',
                ];
                $nodes[] = [
                    'id' => $nodeM['id'],
                    'label' => $nodeM['properties']['name'] ?? 'Unnamed',
                ];
                $edges[] = [
                    'from' => $nodeN['id'],
                    'to' => $nodeM['id'],
                    'label' => $relation['type'] ?? 'Relation',
                ];
            }

            // Eliminar nodos duplicados
            $nodes = array_unique($nodes, SORT_REGULAR);

            return response()->json([
                'nodes' => $nodes,
                'edges' => $edges,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los datos: ' . $e->getMessage()], 500);
        }
    }



}
