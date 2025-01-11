<template>
    <transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                <!-- Mostrar el estado según la prop "success" -->
                <div v-if="!success">
                    <!-- Animación de carga -->
                    <div class="flex justify-center">
                        <div class="loader mb-4"></div>
                    </div>
                    <!-- Leyenda de carga -->
                    <p class="text-gray-700 text-lg font-medium">Cargando...</p>
                </div>
                <div v-else>
                    <!-- Ícono o mensaje de éxito -->
                    <div class="flex gap-4">
                        <div class="text-green-500 text-xl mb-4">🎉</div>
                        <p class="text-gray-700 text-lg font-medium">Transacción Exitosa</p>
                    </div>
                    <!-- Botón para continuar -->
                    <button
                        class="mt-4 px-4 py-2 w-full bg-blue-500 text-white rounded hover:bg-blue-600"
                        @click="handleContinue">
                        Continuar
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import {defineProps, defineEmits} from "vue";

// Definir las props
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    success: {
        type: Boolean,
        default: false, // Indica si se muestra el estado de éxito
    },
});

// Emitir eventos
const emit = defineEmits(["update:show", "continue"]);

// Función para manejar el botón "Continuar"
const handleContinue = () => {
    emit("continue"); // Callback al padre
    emit("update:show", false); // Cierra el loader
};
</script>

<style scoped>
/* Ajustar la alineación del contenedor principal */
.fixed {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Animación del loader */
.loader {
    width: 50px;
    height: 50px;
    border: 6px solid #e0e0e0;
    border-top-color: #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Transición para el fade-in y fade-out */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Mantener animación de rotación */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
