import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    base: '/lohia-worldspace/build/',
    plugins: [
        laravel({
            hotFile: '../hot',
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],
    build: {
        outDir: "../build",
        emptyOutDir: false
    }
});