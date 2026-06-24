import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';

const pages = import.meta.glob('./Pages/**/*.vue');

// Force a fresh Vite asset hash when browser caches old bundles too aggressively.
createInertiaApp({
    title: (title) => (title && String(title).trim() ? title : 'Home'),
    resolve: (name) => {
        return resolvePageComponent(`./Pages/${name}.vue`, pages);
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});

router.on('start', () => {
    document.documentElement.classList.add('is-navigating');
});

router.on('finish', () => {
    document.documentElement.classList.remove('is-navigating');
});
