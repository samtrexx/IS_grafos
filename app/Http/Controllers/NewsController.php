<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class NewsController extends Controller
{
    public function fetchNews()
    {
        // 1. Obtener las noticias de NewsAPI
        $client = new Client();
        $response = $client->request('GET', 'https://newsapi.org/v2/top-headlines', [
            'query' => [
                'apiKey' => '8b53a847ebb34ffd91b0162c3f16c9b1', // Reemplaza con tu clave de NewsAPI
                'country' => 'us',  // Puedes cambiar el país según lo necesites
            ]
        ]);

        $newsData = json_decode($response->getBody()->getContents(), true);

        // 2. Conectar a Neo4j mediante API REST (HTTP)
        $neo4jClient = new Client();

        // 3. Guardar las noticias en la base de datos de Neo4j
        foreach ($newsData['articles'] as $article) {
            $cypherQuery = '
            MERGE (n:Article {title: $title, description: $description, url: $url, publishedAt: $publishedAt})
            RETURN n
            ';

            // Hacer la solicitud POST a la nueva API REST de Neo4j
            $neo4jResponse = $neo4jClient->request('POST', 'http://localhost:7474/db/neo4j/tx/commit', [
                'json' => [
                    'statements' => [
                        [
                            'statement' => $cypherQuery,
                            'parameters' => [
                                'title' => $article['title'],
                                'description' => $article['description'],
                                'url' => $article['url'],
                                'publishedAt' => $article['publishedAt'],
                            ],
                        ],
                    ],
                ],
                'auth' => ['neo4j', 'hallo1234'], // Credenciales para la base de datos Neo4j (sin espacio extra)
            ]);

            // Si quieres procesar la respuesta de Neo4j, puedes hacerlo aquí
            $neo4jResult = json_decode($neo4jResponse->getBody()->getContents(), true);
        }

        return response()->json($newsData['articles']);
    }
}
