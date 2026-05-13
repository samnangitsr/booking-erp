import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (!id.includes('node_modules')) {
                        return undefined;
                    }

                    if (id.includes('react') || id.includes('@inertiajs')) {
                        return 'react-vendor';
                    }

                    if (id.includes('datatables.net')) {
                        return 'datatables-vendor';
                    }

                    if (
                        id.includes('bootstrap') ||
                        id.includes('@popperjs') ||
                        id.includes('flatpickr') ||
                        id.includes('tom-select') ||
                        id.includes('sweetalert2') ||
                        id.includes('jquery')
                    ) {
                        return 'admin-vendor';
                    }

                    return 'vendor';
                },
            },
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
            },
        },
    },
    optimizeDeps: {
        include: [
            'bootstrap',
            'jquery',
            'flatpickr',
            'tom-select',
            'sweetalert2',
            'datatables.net-bs5',
            'datatables.net-buttons-bs5',
        ],
        ignoreOutdatedRequests: true,
    },
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/sass/admin.scss',
                'resources/js/admin.js',
                'resources/js/admin/bookings-form.js',
                'resources/js/inertia/app.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    server: {
        host: '127.0.0.1',
        cors: {
            origin: '*',
        },
        hmr: {
            host: '127.0.0.1',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
