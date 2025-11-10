import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { PurgeCSS } from 'purgecss';
import { resolve } from 'path';
import { readFileSync, writeFileSync } from 'fs';

// Custom Vite plugin to run PurgeCSS
function purgeCSSPlugin() {
    return {
        name: 'vite-plugin-purgecss',
        enforce: 'post',
        async generateBundle(options, bundle) {
            if (process.env.NODE_ENV !== 'production') return;

            for (const fileName in bundle) {
                if (fileName.endsWith('.css')) {
                    const file = bundle[fileName];

                    try {
                        const purgeCSSResults = await new PurgeCSS().purge({
                            content: [
                                './resources/**/*.blade.php',
                                './resources/**/*.js',
                                './resources/**/*.vue',
                                './resources/**/*.html',
                            ],
                            css: [{
                                raw: file.source
                            }],
                            safelist: {
                                standard: ['active', 'show', 'removing', 'hidden', 'visible'],
                                greedy: [/active$/, /^vb-toast/, /^vb-snackbar/]
                            },
                            defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || []
                        });

                        if (purgeCSSResults && purgeCSSResults[0]) {
                            const originalSize = Buffer.byteLength(file.source, 'utf8');
                            const newSize = Buffer.byteLength(purgeCSSResults[0].css, 'utf8');
                            console.log(`PurgeCSS: ${fileName} reduced from ${originalSize} to ${newSize} bytes (${Math.round((1 - newSize/originalSize) * 100)}% reduction)`);
                            file.source = purgeCSSResults[0].css;
                        }
                    } catch (error) {
                        console.warn('PurgeCSS failed:', error);
                    }
                }
            }
        }
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        purgeCSSPlugin(),
    ],
    build: {
        // Target modern browsers to reduce bundle size
        target: 'es2020',
        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                passes: 2,
            },
        },
        // Optimize chunk size
        rollupOptions: {
            output: {
                manualChunks: undefined,
            }
        }
    },
});
