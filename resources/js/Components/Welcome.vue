<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" />

            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Bienvenido!
            </h1>

            <p class="mt-6 text-gray-500 leading-relaxed">
                Bienvenido a <strong>Repaso de Cuentas</strong>, tu aliado en la gestión financiera inteligente.
                Creemos que manejar tu dinero no tiene por qué ser complicado. Nuestra plataforma está diseñada
                para ayudarte a visualizar tus finanzas, establecer metas financieras y tomar decisiones informadas.
                Ya sea que estés empezando a tomar control de tus finanzas o busques maximizar el rendimiento de
                tus ahorros, estamos aquí para simplificar el proceso y empoderarte.
                <br><br>
                Comienza hoy y transforma tu relación con el dinero. Porque cada cuenta cuenta.
            </p>

        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">

            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-gray-900">
                        <a href="https://www.rankia.com/foro/educacion-financiera">Foros de finanzas</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Si estás buscando un espacio donde compartir y aprender sobre finanzas personales, inversiones y educación financiera, te invito a unirte al foro.
                    Es una comunidad activa de expertos y entusiastas que comparten consejos útiles, estrategias de inversión y recursos educativos.
                    Ya sea que estés comenzando o buscando profundizar tus conocimientos,  es el lugar perfecto para encontrar información relevante, resolver dudas y conectarte con otros interesados en mejorar sus finanzas.
                    </p>

                <p class="mt-4 text-sm">
                    <a href="https://www.rankia.com/webinars" class="inline-flex items-center font-semibold text-indigo-700">
                        Webinars

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-indigo-500">
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
                        <a href="https://www.youtube.com/watch?v=r798zWO8P4s&list=PLsvIvv5pZvG4Q9VQtn6c1iiq2mQP2Kanc">Videos Informativos</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                    Si te interesa aprender más sobre educación financiera, te recomiendo esta lista de reproducción en YouTube. Aquí encontrarás videos que cubren temas clave sobre finanzas personales, ahorro e inversiones, de manera clara y accesible. Es una excelente forma de iniciarte o ampliar tus conocimientos sobre cómo gestionar mejor tu dinero. ¡No te la pierdas!
                </p>

                <p class="mt-4 text-sm">
                    <a href="https://www.youtube.com/watch?v=r798zWO8P4s&list=PLsvIvv5pZvG4Q9VQtn6c1iiq2mQP2Kanc" class="inline-flex items-center font-semibold text-indigo-700">
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
