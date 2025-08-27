import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura';
import Layout from "./Layouts/Layout.vue"
import { ZiggyVue } from 'ziggy-js';
import { Ziggy } from './ziggy.js';

createInertiaApp({
    title: title => title ? `${import.meta.env.VITE_APP_NAME} | ${title}` : import.meta.env.VITE_APP_NAME,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page = pages[`./Pages/${name}.vue`]
        page.default.layout = page.default.layout || Layout
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: '.dark-mode',
                    }
                },
                ripple: true,
            })
            .use(plugin)
            .mount(el)
    },
})
