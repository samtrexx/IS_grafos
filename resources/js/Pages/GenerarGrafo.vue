<template>
    <AppLayout title="Generar Grafo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Generar Grafo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="generateGrafo">
                        <div class="form-group">
                            <label for="query">Término de Búsqueda:</label>
                            <input type="text" v-model="query" id="query" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Generar Grafo</button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const query = ref('');

const generateGrafo = async () => {
    try {
        await axios.post(route('guardar-grafo'), { query: query.value });
        alert('Grafo generado exitosamente');
    } catch (error) {
        console.error(error);
        alert('Error al generar el grafo');
    }
};
</script>
