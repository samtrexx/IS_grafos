<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class NewsController extends Controller
{
    //SIn enpoint
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
            SET n.author = $author,
                n.description = $description,
                n.url = $url,
                n.urlToImage = $urlToImage,
                n.publishedAt = $publishedAt,
                n.content = $content
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
                                    'author' => $article['author'] ?? 'Unknown author',
                                    'description' => $article['description'] ?? 'No description',
                                    'url' => $article['url'],
                                    'urlToImage' => $article['urlToImage'] ?? '',
                                    'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                                    'content' => $article['content'] ?? 'No content',
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
                    'author' => $article['author'] ?? 'Unknown author',
                    'title' => $article['title'] ?? 'No title',
                    'description' => $article['description'] ?? 'No description',
                    'url' => $article['url'],
                    'urlToImage' => $article['urlToImage'] ?? '',
                    'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                    'content' => $article['content'] ?? 'No content',
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
    public function fetchNewsByTheme($theme)
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

        // Filtrar artículos por tema (en el título)
        $filteredArticles = array_filter($newsData['articles'], function($article) use ($theme) {
            return stripos($article['title'], $theme) !== false;
        });

        // Si no se encuentra ningún artículo que coincida
        if (empty($filteredArticles)) {
            return response()->json(['message' => 'No articles found for the given theme'], 404);
        }

        // Crear nodos y relaciones para el grafo
        $nodes = [];
        $edges = [];

        foreach ($filteredArticles as $index => $article) {
            $nodes[] = [
                'id' => uniqid(),
                'label' => $article['title'] ?? 'Untitled',
                'author' => $article['author'] ?? 'Unknown author',
                'description' => $article['description'] ?? 'No description',
                'url' => $article['url'],
                'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                'content' => $article['content'] ?? 'No content',
            ];

            // Crear relaciones basadas en contenido similar entre artículos
            for ($i = 0; $i < count($filteredArticles); $i++) {
                for ($j = $i + 1; $j < count($filteredArticles); $j++) {
                    $article1 = $filteredArticles[$i];
                    $article2 = $filteredArticles[$j];
                    // Comparar si el contenido de los dos artículos es similar
                    if ($this->isContentSimilar($article1['content'], $article2['content'])) {
                        // Crear una relación entre los artículos
                        $edges[] = [
                            'from' => $nodes[$i]['id'],
                            'to' => $nodes[$j]['id'],
                            'label' => 'Related by Content',
                        ];
                    }
                }
            }

        }

        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
            'message' => "Articles filtered by theme: {$theme}",
        ]);
    }

    public function fetchNewsByAuthor($author)
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

        // Filtrar artículos por autor
        $filteredArticles = array_filter($newsData['articles'], function($article) use ($author) {
            return isset($article['author']) && strpos(strtolower($article['author']), strtolower($author)) !== false;
        });

        // Si no se encuentra ningún artículo que coincida
        if (empty($filteredArticles)) {
            return response()->json(['message' => 'No articles found for the given author'], 404);
        }

        // Crear nodos y relaciones para el grafo
        $nodes = [];
        $edges = [];

        foreach ($filteredArticles as $index => $article) {
            $nodes[] = [
                'id' => uniqid(),
                'label' => $article['title'] ?? 'Untitled',
                'author' => $article['author'] ?? 'Unknown author',
                'description' => $article['description'] ?? 'No description',
                'url' => $article['url'],
                'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                'content' => $article['content'] ?? 'No content',
            ];
        }

        // Crear relaciones basadas en contenido similar entre artículos
        for ($i = 0; $i < count($filteredArticles); $i++) {
            for ($j = $i + 1; $j < count($filteredArticles); $j++) {
                $article1 = $filteredArticles[$i];
                $article2 = $filteredArticles[$j];

                // Comparar si el contenido de los dos artículos es similar
                if ($this->isContentSimilar($article1['content'], $article2['content'])) {
                    // Crear una relación entre los artículos
                    $edges[] = [
                        'from' => $nodes[$i]['id'],
                        'to' => $nodes[$j]['id'],
                        'label' => 'Related by Content',
                    ];
                }
            }
        }

        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
            'message' => "Articles filtered by author: {$author}",
        ]);
    }

    public function fetchNewsByDate($date)
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

        // Filtrar artículos por fecha de publicación
        $filteredArticles = array_filter($newsData['articles'], function($article) use ($date) {
            return isset($article['publishedAt']) && Carbon::parse($article['publishedAt'])->format('Y-m-d') === Carbon::parse($date)->format('Y-m-d');
        });

        // Si no se encuentra ningún artículo que coincida
        if (empty($filteredArticles)) {
            return response()->json(['message' => 'No articles found for the given date'], 404);
        }

        // Crear nodos y relaciones para el grafo
        $nodes = [];
        $edges = [];

        foreach ($filteredArticles as $index => $article) {
            $nodes[] = [
                'id' => uniqid(),
                'label' => $article['title'] ?? 'Untitled',
                'author' => $article['author'] ?? 'Unknown author',
                'description' => $article['description'] ?? 'No description',
                'url' => $article['url'],
                'publishedAt' => $article['publishedAt'] ?? 'Unknown date',
                'content' => $article['content'] ?? 'No content',
            ];
        }

        // Crear relaciones basadas en descripción y contenido similar entre artículos
        for ($i = 0; $i < count($nodes); $i++) {
            for ($j = $i + 1; $j < count($nodes); $j++) {
                $description1 = $nodes[$i]['description'];
                $description2 = $nodes[$j]['description'];
                $content1 = $nodes[$i]['content'];
                $content2 = $nodes[$j]['content'];

                // Comparar descripción o contenido
                if ($this->isContentSimilar($description1, $description2) || $this->isContentSimilar($content1, $content2)) {
                    $edges[] = [
                        'from' => $nodes[$i]['id'],
                        'to' => $nodes[$j]['id'],
                        'label' => 'Related by Description or Content',
                    ];
                }
            }
        }


        return response()->json([
            'nodes' => $nodes,
            'edges' => $edges,
            'message' => "Articles filtered by date: {$date}",
        ]);
    }

    // Metodo para comparar si los contenidos de dos artículos son similares
    private function isContentSimilar($content1, $content2)
    {
        // Implementa aquí la lógica de comparación, por ejemplo, mediante una verificación de similitud de texto
        similar_text($content1, $content2, $percent);
        return $percent > 70; // Si la similitud es mayor al 70%, consideramos que los contenidos son similares
    }



}
