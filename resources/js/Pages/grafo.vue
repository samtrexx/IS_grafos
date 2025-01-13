<script setup lang="ts">
import FormLayout from "@/Pages/Layouts/FormLayout.vue";
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    tarjeta_numero: string;
    tarjeta_id: number;
}>();

if (!props.tarjeta_id) {
    console.error("El ID de la tarjeta no está definido.");
}

const form = useForm({
    tarjeta_id: props.tarjeta_id,
    Monto: '',
    tipo_liquidacion: 'pago',
    estado: 'pendiente',
    fecha_liquidacion: '',
});

const submit = () => {
    if (!form.tarjeta_id) {
        console.error("El ID de la tarjeta es inválido.");
        return;
    }

    form.post('/tarjeta-credito/pagar', {
        onSuccess: () => console.log('Formulario enviado exitosamente.'),
        onError: () => console.error('Error al enviar el formulario.'),
    });
};
</script>
<template>
    <form-layout>
        <form @submit.prevent="submit">
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label for="tarjeta_numero">Número de Tarjeta</label>
                    <input type="text" id="tarjeta_numero" class="input" :value="props.tarjeta_numero" readonly />
                </div>
                <div>
                    <label for="Monto">Monto</label>
                    <input type="number" id="Monto" class="input" step="0.001" min="0" v-model="form.Monto" required />
                </div>
                <div>
                    <label for="tipo_liquidacion">Tipo de Liquidación</label>
                    <select id="tipo_liquidacion" class="input" v-model="form.tipo_liquidacion" required>
                        <option value="pago">Pago</option>
                        <option value="ajuste">Ajuste</option>
                        <option value="reembolso">Reembolso</option>
                    </select>
                </div>
                <div>
                    <label for="estado">Estado</label>
                    <select id="estado" class="input" v-model="form.estado" required>
                        <option value="pendiente">Pendiente</option>
                        <option value="completado">Completado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div>
                    <label for="fecha_liquidacion">Fecha de Liquidación</label>
                    <input type="date" id="fecha_liquidacion" class="input" v-model="form.fecha_liquidacion" required />
                </div>
                <div>
                    <button type="submit" class="btn-primary">Enviar</button>
                </div>
            </div>
        </form>
    </form-layout>
</template>
