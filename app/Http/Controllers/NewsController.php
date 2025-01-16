<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NewsController extends Controller
{
    /*
    public function fetchNews()
    {
        // Obtener las noticias de NewsAPI
        $client = new Client();
        $response = $client->request('GET', 'https://newsapi.org/v2/top-headlines', [
            'query' => [
                'apiKey' => '8b53a847ebb34ffd91b0162c3f16c9b1', // Reemplaza con tu clave de NewsAPI
                'country' => 'us',  // Puedes cambiar el país según lo necesites
            ]
        ]);

        $newsData = json_decode($response->getBody()->getContents(), true);

        // Validar que existan artículos antes de proceder
        if (empty($newsData['articles'])) {
            return response()->json(['message' => 'No news articles found'], 404);
        }

        // Conectar a Neo4j mediante API REST
        $neo4jClient = new Client();

        foreach ($newsData['articles'] as $article) {
            try {
                $cypherQuery = '
                MERGE (n:Article {title: $title})
                SET n.description = $description,
                    n.url = $url,
                    n.publishedAt = $publishedAt
                RETURN n
                ';

                // Hacer la solicitud POST a la API REST de Neo4j
                //     http://localhost:7474/db/data/transaction/commit
                //      http://localhost:7474/db/data/tx/commit

                $neo4jResponse = $neo4jClient->request('POST', 'http://localhost:7474/db/data/tx/commit', [
                    'json' => [
                        'statements' => [
                            [
                                'statement' => $cypherQuery,
                                'parameters' => [
                                    'title' => $article['title'],
                                    'description' => $article['description'] ?? 'No description',
                                    'url' => $article['url'],
                                    'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                                ],
                            ],
                        ],
                    ],
                    'auth' => ['neo4j', 'hallo1234'], // Credenciales para Neo4j
                ]);

                // Procesar la respuesta de Neo4j si es necesario
                $neo4jResult = json_decode($neo4jResponse->getBody()->getContents(), true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Articles saved successfully', 'articles' => $newsData['articles']]);
    }
*/

    public function fetchNews()
    {
        // Obtener las noticias de NewsAPI
        $client = new Client();
        $response = $client->request('GET', 'https://newsapi.org/v2/top-headlines', [
            'query' => [
                'apiKey' => '8b53a847ebb34ffd91b0162c3f16c9b1', // Reemplaza con tu clave de NewsAPI
                'country' => 'us',  // Puedes cambiar el país según lo necesites
            ]
        ]);

        $newsData = json_decode($response->getBody()->getContents(), true);

        // Validar que existan artículos antes de proceder
        if (empty($newsData['articles'])) {
            return response()->json(['message' => 'No news articles found'], 404);
        }

        // Conectar a Neo4j mediante API REST
        $neo4jClient = new Client();

        // Estructura para nodos y relaciones
        $nodes = [];
        $edges = [];

        foreach ($newsData['articles'] as $article) {
            try {
                $cypherQuery = '
            MERGE (n:Article {title: $title})
            SET n.description = $description,
                n.url = $url,
                n.publishedAt = $publishedAt
            RETURN n
            ';

                // Hacer la solicitud POST a la API REST de Neo4j
                $neo4jResponse = $neo4jClient->request('POST', 'http://localhost:7474/db/data/tx/commit', [
                    'json' => [
                        'statements' => [
                            [
                                'statement' => $cypherQuery,
                                'parameters' => [
                                    'title' => $article['title'],
                                    'description' => $article['description'] ?? 'No description',
                                    'url' => $article['url'],
                                    'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                                ],
                            ],
                        ],
                    ],
                    'auth' => ['neo4j', 'hallo1234'], // Credenciales para Neo4j
                ]);

                // Suponiendo que se crea un nodo en Neo4j, lo añadimos al array de nodos
                $nodes[] = [
                    'id' => uniqid(), // Usamos un ID único para cada nodo
                    'label' => $article['title'] ?? 'Untitled',
                ];

                // Crear relaciones, por ejemplo entre artículos
                if (count($nodes) > 1) {
                    $edges[] = [
                        'from' => $nodes[count($nodes) - 2]['id'],
                        'to' => $nodes[count($nodes) - 1]['id'],
                        'label' => 'Related',
                    ];
                }

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        // Devolver la respuesta en formato de nodos y relaciones
        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
            'message' => 'Articles saved successfully',
        ]);
    }


}
