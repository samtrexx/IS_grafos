import 'bootstrap-icons/font/bootstrap-icons.css';
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Importar vis-network
import 'vis-network/styles/vis-network.css';
import { Network } from 'vis-network/standalone';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ZiggyVue);

        // Agregar globalmente vis-network si es necesario
        app.config.globalProperties.$visNetwork = Network;

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
