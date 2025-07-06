// New Vite config
import { defineConfig } from 'vite'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
    resolve: {
        alias: {
            '@elysium': fileURLToPath(new URL('.', import.meta.url))
        },
    },
});
