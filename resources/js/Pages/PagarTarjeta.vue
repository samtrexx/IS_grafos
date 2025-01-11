<script setup lang="ts">

import FormLayout from "@/Pages/Layouts/FormLayout.vue";
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
// Definir las props
const props = defineProps<{
    tarjeta_numero: string;
    tarjeta_id: number;
}>();

if (props.tarjeta_id === undefined || props.tarjeta_id === null) {
    console.error("El ID de la tarjeta no está definido.");
}

const form = useForm({
    tarjeta_id: props.tarjeta_id,
    Monto: '',
    tipo_liquidacion: 'pago',
    estado: 'pendiente',
    fecha_liquidacion: '',
});

// Función para enviar el formulario
const submit = () => {
    // Validar tarjeta_id antes de enviar
    if (form.tarjeta_id === undefined || form.tarjeta_id === null) {
        console.error("El ID de la tarjeta es inválido.");
        return;
    }

    const queryParams = new URLSearchParams({
        Monto: form.Monto.toString(),
        tipo_liquidacion: form.tipo_liquidacion,
        estado: form.estado,
        fecha_liquidacion: form.fecha_liquidacion,
    }).toString();

    // Asegúrate de incluir tarjeta_id en la URL
    form.get(`/tarjeta-credito/pagar/${form.tarjeta_id}?${queryParams}`); // Llamada al controlador con parámetros de consulta
};
</script>

<template>
        <form-layout>
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label for="tarjeta_numero">Número de Tarjeta</label>
                    <input type="text" name="tarjeta_numero" id="tarjeta_numero" class="input" :value="props.tarjeta_numero" readonly/>
                </div>
                <div>
                    <label for="Monto">Monto</label>
                    <input type="number" name="Monto" id="Monto" class="input" step="0.001" min="0" required>
                </div>
                <div>
                    <label for="tipo_liquidacion">Tipo de Liquidación</label>
                    <select name="tipo_liquidacion" id="tipo_liquidacion" class="input" required>
                        <option value="pago">Pago</option>
                        <option value="ajuste">Ajuste</option>
                        <option value="reembolso">Reembolso</option>
                    </select>
                </div>
                <div>
                    <label for="estado">Estado</label>
                    <select name="estado" id="estado" class="input" required>
                        <option value="pendiente">Pendiente</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div>
                    <label for="fecha_liquidacion">Fecha de Liquidación</label>
                    <input type="date" name="fecha_liquidacion" id="fecha_liquidacion" class="input" required>
                </div>
            </div>



        </form-layout>
</template>

<style scoped>

</style>
