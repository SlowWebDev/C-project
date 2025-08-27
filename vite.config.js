import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/js/app.js',
                'resources/js/main.js',
                'resources/js/admin.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        target: 'es2015',
        cssTarget: 'chrome80',
        cssCodeSplit: true,
        minify: 'esbuild',
        reportCompressedSize: false,
        chunkSizeWarningLimit: 1000,
        assetsInlineLimit: 4096,
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        if (id.includes('swiper')) return 'swiper';
                        if (id.includes('aos') || id.includes('gsap')) return 'animations';
                        return 'vendor';
                    }
                },
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
                        return `assets/images/[name]-[hash][extname]`;
                    }
                    if (/css/i.test(ext)) {
                        return `assets/css/[name]-[hash][extname]`;
                    }
                    return `assets/[name]-[hash][extname]`;
                }
            }
        }
    },
    optimizeDeps: {
        include: ['swiper', 'aos'],
        exclude: ['@fortawesome/fontawesome-free']
    },
    css: {
        devSourcemap: false
    }
});
