<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

import { onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';

onMounted(() => {
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

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" />

            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Bienvenido!
            </h1>
            <div id="grafo-container" style="height: 400px; margin-top: 10px;"></div>

        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">

            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-gray-900">
                        <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Api de noticias</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Apis 
                </p>

                <p class="mt-4 text-sm">
                    <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0" class="inline-flex items-center font-semibold text-indigo-700">
                        Pag de la api

                        <svg xmlns="https://www.youtube.com/watch?v=xvFZjo5PgG0" viewBox="0 0 20 20" class="ms-1 size-5 fill-indigo-500">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                        <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-gray-900">
                        <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Videos </a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    De grafos
                </p>

                <p class="mt-4 text-sm">
                    <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0" class="inline-flex items-center font-semibold text-indigo-700">
                        Empezar a ver

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-indigo-500">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>



        </div>
    </div>
</template>
