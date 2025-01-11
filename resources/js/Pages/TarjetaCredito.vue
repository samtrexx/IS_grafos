<script setup lang="ts">

import FormLayout from "@/Pages/Layouts/FormLayout.vue";
import AppLayout from '@/Layouts/AppLayout.vue';

</script>

<template>
    <AppLayout title="Tarjetas">

    <form-layout>
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="input" required>
            </div>
            <div>
                <label for="no_tarjeta">No. Tarjeta</label>
                <input type="text" name="no_tarjeta" id="no_tarjeta" class="input" required>
            </div>
            <div>
                <label for="tipo">Tipo</label>
                <input type="text" name="tipo" id="tipo" class="input" required>
            </div>
            <div>
                <label for="limite">Límite</label>
                <input type="number" name="limite" id="limite" class="input" step="0.01"  required>
            </div>
            <div>
                <label for="fecha_corte">Fecha de Corte</label>
                <input type="date" name="fecha_corte" id="fecha_corte" class="input" required>
            </div>
            <div>
                <label for="tasa_interes">Tasa de Interés (%) </label>
                <input type="number" name="tasa_interes" id="tasa_interes" class="input" step="0.1" >
            </div>
        </div>

        <div>
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="4" class="input"></textarea>
        </div>

    </form-layout>
    </AppLayout>
</template>

<style scoped>

</style>
