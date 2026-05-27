import path from 'path';
import { defineConfig } from 'vite';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    root: path.resolve(__dirname),
    base: '',
    build: {
        manifest: true,
        emptyOutDir: true,
        outDir: 'dist',
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'src/main.js'),
                editor: path.resolve(__dirname, 'src/editor.js'),
                'bm-custom-select': path.resolve(__dirname, 'src/js/bm-custom-select.js'),
                'rank-math-faq': path.resolve(__dirname, 'src/js/rank-math-faq.js'),
            },
            output: {
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
    },
});
