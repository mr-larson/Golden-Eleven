import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import PageTransition from './Layouts/PageTransition.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Golden Eleven';

createInertiaApp({
    title: (title) => title && title !== appName ? `${title} - ${appName}` : appName,
    resolve: async (name) => {
        const { default: page } = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        );

        page.layout ??= PageTransition;
        return page;
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },

});
