<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use jcobhams\NewsApi\NewsApi;
use Symfony\Component\DomCrawler\Crawler;
use voku\helper\StopWords;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Stemmers\PorterStemmer;
class NewsController extends Controller
{

    //SIn enpoint
    public function fetchNews()
    {
        // Obtener las noticias de NewsAPI
        $newsapi = new NewsApi('b9c11cd1c1f745b2b9647816b3b214d5');

        #$client = new Client();
        $query='coronavirus';
        $language='es';
        $sort_by='relevancy';

        $all_articles = $newsapi->getEverything($query, null, null, null, null, null, $language, $sort_by, null, null);

        #$response = $client->request('GET', 'https://newsapi.org/v2/top-headlines', [
        #    'query' => [
        #        'apiKey' => '8b53a847ebb34ffd91b0162c3f16c9b1', // Reemplaza con tu clave de NewsAPI
        #        'country' => 'us',  // Puedes cambiar el país según lo necesites
        #    ]
        #]);
        $articles_data = json_decode(json_encode($all_articles), true);
        #$newsData = json_decode($all_articles, true);

        // Validar que existan artículos antes de proceder
        if (empty($newsData['articles']))
        {
            return response()->json(['message' => 'No news articles found'], 404);
        }
        foreach ($articles_data['articles'] as $article)
        {
            $output .= "Título: " . $article['title'] . PHP_EOL;
            $output .= "Descripción: " . $article['description'] . PHP_EOL;
            $output .= "Contenido truncado: " . $article['content'] . PHP_EOL;
            $output .= "URL: " . $article['url'] . PHP_EOL;

            // Hacer una solicitud HTTP para obtener el contenido completo del artículo
            try {
                $response = $client->request('GET', $article['url']);
                $html = $response->getBody()->getContents();

                // Utilizar Symfony DomCrawler para analizar el contenido
                $crawler = new Crawler($html);

                // Extraer el contenido del artículo basado en el HTML específico de la página
                // Nota: Necesitarás ajustar el selector CSS según la estructura de la página web
                $content = $crawler->filter('p')->each(function (Crawler $node, $i) {
                    return $node->text();
                });

                $output .= "Contenido completo: " . implode(" ", $content) . PHP_EOL;
            } catch (Exception $e) {
                $output .= "No se pudo extraer el contenido completo del artículo. Error: " . $e->getMessage() . PHP_EOL;
            }

            $output .= PHP_EOL;
        }
        // Convertir a minúsculas
        $output = mb_strtolower($output, 'UTF-8');
        // Eliminar acentos
        $output = preg_replace('/[áàäâã]/u', 'a', $output);
        $output = preg_replace('/[éèëê]/u', 'e', $output);
        $output = preg_replace('/[íìïî]/u', 'i', $output);
        $output = preg_replace('/[óòöôõ]/u', 'o', $output);
        $output = preg_replace('/[úùüû]/u', 'u', $output);
        $output = preg_replace('/[ñ]/u', 'n', $output);
        $output = preg_replace('/[ç]/u', 'c', $output);
        // Eliminar signos de puntuación
        $output = preg_replace('/[^\w\s]/u', '', $output);
        // Eliminar más de un espacio en blanco
        $output = preg_replace('/\s+/', ' ', $output);
        // Eliminar espacios al inicio y al final
        $output = trim($output);
        //Tokenizar
        $tokenizer = new WhitespaceTokenizer();
        $tokens = $tokenizer->tokenize($output);
        //Eliminar StopWords
        $stopWords = new StopWords();
        $stopWordsArray = $stopWords->getStopWordsFromLanguage('es');
        $tokens = array_diff($tokens, $stopWordsArray);
        //Stemming---Lemmatization
        #$stemmer = new PorterStemmer();
        #$stemmedTokens = array_map([$stemmer, 'stem'], $tokens);
        $command = escapeshellcmd("python3 python/lemma.py " . escapeshellarg($texto)); $output = shell_exec($command); // Decodificar el JSON de la salida de Python $lemmatized_tokens = json_decode($output, true);
        //Unir a texto limpio
        $textoLimpio = implode(' ', $stemmedTokens);
        $textoLimpio = trim($textoLimpio);
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
