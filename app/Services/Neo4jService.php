<?php

namespace App\Services;

use GuzzleHttp\Client;

class Neo4jService
{
    protected $client;
    protected $baseUrl;
    protected $auth;

    public function __construct()
    {
        // Configuración para conectar con Neo4j
        $this->baseUrl = env('NEO4J_URL', 'http://localhost:7474/db/data/tx/commit');
        $this->auth = [
            env('NEO4J_USERNAME', 'neo4j'), // Usuario de Neo4j
            env('NEO4J_PASSWORD', 'hallo1234'), // Contraseña de Neo4j
        ];

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => $this->auth,
        ]);
    }

    /**
     * Ejecuta una consulta Cypher en Neo4j.
     *
     * @param string $query
     * @param array $parameters
     * @return array
     */
    public function runQuery(string $query, array $parameters = []): array
    {
        try {
            $response = $this->client->post('', [
                'json' => [
                    'statements' => [
                        [
                            'statement' => $query,
                            'parameters' => $parameters,
                        ],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['results'] ?? [];
        } catch (\Exception $e) {
            throw new \RuntimeException('Error ejecutando la consulta Cypher: ' . $e->getMessage());
        }
    }
}
