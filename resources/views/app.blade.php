<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        <script type="text/javascript" src="https://unpkg.com/vis-network@9.1.0/dist/vis-network.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        @inertia

       
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Obtener nodos y relaciones desde tu base de datos Neo4j
                const nodes = new vis.DataSet([
                    {id: 1, label: 'Node 1'},
                    {id: 2, label: 'Node 2'},
                    // Añade más nodos aquí
                ]);
                const edges = new vis.DataSet([
                    {from: 1, to: 2, label: 'Relationship'},
                    // Añade más relaciones aquí
                ]);

                const container = document.getElementById('grafo-container');
                const data = {
                    nodes: nodes,
                    edges: edges
                };
                const options = {
                    interaction: {
                        dragNodes: true,
                        zoomView: true
                    }
                };
                const network = new vis.Network(container, data, options);

                // Mostrar información de un nodo al hacer clic
                network.on('click', function (params) {
                    if (params.nodes.length > 0) {
                        const nodeId = params.nodes[0];
                        const node = nodes.get(nodeId);
                        // Aquí puedes hacer una llamada a la API de Wikidata para obtener información adicional
                        alert('Información del nodo: ' + JSON.stringify(node));
                    }
                });
            });
        </script>
    </body>
</html>
