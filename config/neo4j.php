<?php

return [
    'host' => env('NEO4J_HOST', 'bolt://localhost'),
    'username' => env('NEO4J_USERNAME', 'neo4j'),
    'password' => env('NEO4J_PASSWORD', 'secret'),
];
