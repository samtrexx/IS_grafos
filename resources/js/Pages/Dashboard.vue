<script setup>
import { onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Inicio
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
                <!-- Contenedor para el grafo -->
                <div id="grafo-container" style="height: 600px; margin-top: 20px;"></div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
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
