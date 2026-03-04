import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import { resolve, basename } from "node:path";
import { readdirSync } from "node:fs";

function getEntries() {
    const dir = resolve(__dirname, "src");
    const files = readdirSync(dir, { recursive: false }).filter(file => file.indexOf(".") !== -1);

    // Key bestimmt später den Output-Namen: js/<key>.js
    // ContextMenu.vue => ContextMenu
    const input: Record<string, string> = {};
    for (const f of files) {
        const key = basename(f).replace(/\.\w+$/, "");
        input[key] = resolve(dir, f);
    }
    return input;
}

export default defineConfig(() => {
    return {
        base: "/slothsoft@amber/",
        plugins: [vue()],

        build: {
            outDir: resolve(__dirname, "../assets/vue"),
            emptyOutDir: true,
            sourcemap: false,
            copyPublicDir: false,
            cssCodeSplit: true,

            rollupOptions: {
                input: getEntries(),
                output: {
                    format: "es",
                    entryFileNames: "[name].js",
                    chunkFileNames: "chunks/[name].js",
                    assetFileNames: (assetInfo) => {
                        if (assetInfo.names) {
                            return assetInfo.names[0];
                        }
                        return "assets/[name][extname]";
                    }
                },
                external: [
                    "/slothsoft@farah/js/Bootstrap",
                    "/slothsoft@amber/js/AmberAPI",
                    "/slothsoft@farah/js/XMLNamespaces",
                ],
            },

            minify: false
        }
    };
});