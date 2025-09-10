import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom.css',
                'resources/css/charts.css',
                'resources/css/mobile.css',
                'resources/css/statistics.css',
                'resources/js/app.js',
                'resources/js/course-mobile-charts.js',
                'resources/js/bootstrap.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5174,
        hmr: {
            host: 'localhost',
            protocol: 'ws'
        }
    },
});
