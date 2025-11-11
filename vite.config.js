import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';
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
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: null,
            strategies: 'generateSW',
            minify: true,
            workbox: {
                globPatterns: ['**/*.{js,css,woff2,ico,jpg,png}'],
                runtimeCaching: [
                    {
                        urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'google-fonts-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365 // 365 days
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    {
                        urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'gstatic-fonts-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365 // 365 days
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    {
                        urlPattern: /\/build\/assets\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'assets-cache',
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60 * 24 * 30 // 30 days
                            }
                        }
                    },
                    {
                        urlPattern: /\/fonts\/.*/i,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'local-fonts-cache',
                            expiration: {
                                maxEntries: 10,
                                maxAgeSeconds: 60 * 60 * 24 * 365 // 365 days
                            }
                        }
                    }
                ]
            },
            manifest: {
                name: 'No BS URL Shortener',
                short_name: 'No BS Short',
                description: 'A no-nonsense URL shortening service',
                theme_color: '#000000',
                background_color: '#ffffff',
                display: 'standalone',
                start_url: '/',
                scope: '/',
                icons: [
                    {
                        src: '/favicon.ico',
                        sizes: '48x48',
                        type: 'image/x-icon'
                    }
                ]
            }
        })
    ],
    build: {
        // Enable minification and optimization
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console logs in production
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug']
            },
            mangle: true,
            format: {
                comments: false
            }
        },
        // Better code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor chunks for better caching
                    'vendor': ['./resources/js/bootstrap.js'],
                    'vibe': ['./resources/js/vibe-brutalism.js']
                }
            }
        },
        // Reduce chunk size warnings threshold
        chunkSizeWarningLimit: 600,
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Source maps for production debugging (optional)
        sourcemap: false,
        // Asset optimization
        assetsInlineLimit: 4096 // Inline assets < 4kb as base64
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['axios']
    }
});
