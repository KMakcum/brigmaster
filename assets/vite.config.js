import { defineConfig } from "vite";
import path from "node:path";

const assetsRoot = __dirname;
const srcRoot = path.resolve(assetsRoot, "src");

export default defineConfig({
  base: "./",
  build: {
    outDir: path.resolve(assetsRoot, "dist"),
    emptyOutDir: true,
    sourcemap: false,
    rollupOptions: {
      input: {
        slab: path.resolve(srcRoot, "calculators/slab.js"),
        strip: path.resolve(srcRoot, "calculators/strip.js"),
        pile: path.resolve(srcRoot, "calculators/pile.js"),
        brick: path.resolve(srcRoot, "calculators/brick.js"),
        screed: path.resolve(srcRoot, "calculators/screed.js"),
        drywall: path.resolve(srcRoot, "calculators/drywall.js"),
        tile: path.resolve(srcRoot, "calculators/tile.js")
      },
      output: {
        entryFileNames: "calculators/[name].js",
        chunkFileNames: "shared/[name].js",
        assetFileNames: "assets/[name][extname]"
      }
    }
  }
});
