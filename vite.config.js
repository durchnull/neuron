import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // If you are building an SPA, including applications built using Inertia, Vite works best without CSS entry points:
                // 'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
            detectTls: 'neuron.ddev.site',
        }),
    ],
});
