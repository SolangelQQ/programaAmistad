import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/dropdown-menu.js',
                'resources/js/confirmation.js',
                'public/js/friendships.js',
                'resources/js/buddies.js',
                'resources/js/activities-calendar.js',
                'resources/js/photo-manager.js',
                'resources/js//buddy-form.js',
                'public/js/upcoming-activities.js',
                'public/js/activity-filters.js',
                'public/js/activities-sidebar.js',
                'resources/js/reportes.js',
                'public/js/friendshipModal.js',
                'public/js/follow-up.js',
                'public/js/general.js',
                'public/js/liderazgo-tab.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    resolve: {
        alias: {
            // Ensure public assets can be found
            '@public': '/public'
        },
    },
    publicDir: 'public',
});