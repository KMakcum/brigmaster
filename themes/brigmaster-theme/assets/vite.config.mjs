import { defineConfig } from 'vite';
import {
  readdirSync,
  existsSync,
  statSync,
  readFileSync,
  writeFileSync,
  mkdirSync,
  rmSync,
} from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const PARTIALS_DIR = path.resolve(__dirname, 'src/partials');

function readPartialFile(filePath) {
  return readFileSync(filePath, 'utf8').replace(/^\uFEFF/, '');
}

function renderPartial(template, data) {
  return template.replace(/\{\{(\w+)\}\}/g, (_, key) => data[key] ?? '');
}

function renderFaqSection(dataName) {
  const sectionPath = path.join(PARTIALS_DIR, 'faq-section.html');
  const itemPath = path.join(PARTIALS_DIR, 'faq-item.html');
  const dataPath = path.join(PARTIALS_DIR, 'data', `${dataName}.json`);
  const sectionTpl = readPartialFile(sectionPath);
  const itemTpl = readPartialFile(itemPath);
  const data = JSON.parse(readPartialFile(dataPath));
  const { items, ...sectionMeta } = data;
  const itemsHtml = items.map((item) => renderPartial(itemTpl, item)).join('\n            ');
  return renderPartial(sectionTpl, { ...sectionMeta, itemsHtml });
}

function htmlPartialsPlugin() {
  return {
    name: 'html-partials',
    transformIndexHtml: {
      order: 'pre',
      handler(html) {
        let result = html.replace(/<!--\s*@cards\s+([\w-]+)\s*-->/g, (_, dataName) => {
          const templatePath = path.join(PARTIALS_DIR, 'card-article.html');
          const dataPath = path.join(PARTIALS_DIR, 'data', `${dataName}.json`);
          const template = readPartialFile(templatePath);
          const items = JSON.parse(readPartialFile(dataPath));
          return items.map((item) => renderPartial(template, item)).join('\n            ');
        });
        result = result.replace(/<!--\s*@foundation-types\s+([\w-]+)\s*-->/g, (_, dataName) => {
          const templatePath = path.join(PARTIALS_DIR, 'foundation-type-card.html');
          const dataPath = path.join(PARTIALS_DIR, 'data', `${dataName}.json`);
          const template = readPartialFile(templatePath);
          const items = JSON.parse(readPartialFile(dataPath));
          return items.map((item) => renderPartial(template, item)).join('\n            ');
        });
        result = result.replace(/<!--\s*@faq-section\s+([\w-]+)\s*-->/g, (_, dataName) =>
          renderFaqSection(dataName),
        );
        return result;
      },
    },
  };
}

function htmlOutputPlugin() {
  return {
    name: 'html-output',
    closeBundle() {
      const pagesDir = path.resolve(__dirname, 'dist/pages');
      const htmlDir = path.resolve(__dirname, 'dist/html');
      const pages = [];

      if (!existsSync(pagesDir)) return;

      mkdirSync(htmlDir, { recursive: true });

      for (const pageName of readdirSync(pagesDir)) {
        const htmlPath = path.join(pagesDir, pageName, 'index.html');
        if (!existsSync(htmlPath)) continue;

        let source = readPartialFile(htmlPath).replaceAll('../../', '../');
        if (!source.includes('../css/common.css')) {
          source = source.replace(
            '    <link rel="stylesheet" crossorigin href="../css/',
            '    <link rel="stylesheet" crossorigin href="../css/common.css">\n    <link rel="stylesheet" crossorigin href="../css/',
          );
        }
        writeFileSync(path.join(htmlDir, `${pageName}.html`), source);
        pages.push(pageName);
      }

      const links = pages
        .sort((a, b) => a.localeCompare(b))
        .map((pageName) => `      <li><a href="./${pageName}.html">${pageName}</a></li>`)
        .join('\n');

      writeFileSync(
        path.join(htmlDir, 'index.html'),
        `<!doctype html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Brigmaster pages</title>
    <style>
      :root {
        color-scheme: light;
        font-family:
          Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
        color: #1e293b;
        background: #f4f6fa;
      }

      * {
        box-sizing: border-box;
      }

      body {
        min-height: 100vh;
        margin: 0;
        padding: 48px 20px;
        background:
          linear-gradient(180deg, #ffffff 0, rgb(255 255 255 / 0) 280px),
          #f4f6fa;
      }

      main {
        width: min(100%, 920px);
        margin: 0 auto;
      }

      h1 {
        margin: 0;
        font-size: 40px;
        line-height: 1.15;
        letter-spacing: 0;
      }

      p {
        max-width: 640px;
        margin: 12px 0 0;
        color: #64748b;
        font-size: 16px;
        line-height: 1.6;
      }

      ul {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
        margin: 32px 0 0;
        padding: 0;
        list-style: none;
      }

      a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 56px;
        padding: 16px 18px;
        border: 1px solid #dbe3ec;
        border-radius: 8px;
        color: #1e293b;
        background: #ffffff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition:
          border-color 0.18s ease,
          box-shadow 0.18s ease,
          transform 0.18s ease;
      }

      a::after {
        content: "->";
        color: #5b7c99;
        font-weight: 700;
      }

      a:hover,
      a:focus-visible {
        border-color: #9eb4c8;
        box-shadow: 0 8px 22px rgb(15 23 42 / 0.08);
        transform: translateY(-1px);
        outline: none;
      }
    </style>
  </head>
  <body>
    <main>
      <h1>Brigmaster pages</h1>
      <p>Generated HTML pages for quick review after build.</p>
      <ul>
${links}
      </ul>
    </main>
  </body>
</html>
`,
      );

      rmSync(pagesDir, { recursive: true, force: true });
    },
  };
}

// Discover every directory inside src/pages/ that has an index.html.
// Each one becomes a separate multi-page entry => its own JS + CSS bundle in dist/.
function collectPageInputs() {
  const pagesDir = path.resolve(__dirname, 'src/pages');
  const inputs = {
    common: path.resolve(__dirname, 'src/common.scss'),
    editor: path.resolve(__dirname, 'src/editor.js'),
  };

  if (!existsSync(pagesDir)) return inputs;

  for (const name of readdirSync(pagesDir)) {
    const dir = path.join(pagesDir, name);
    const html = path.join(dir, 'index.html');
    if (statSync(dir).isDirectory() && existsSync(html)) {
      inputs[name] = html;
    }
  }
  return inputs;
}

function getAssetFileName(assetInfo) {
  const ext = path.extname(assetInfo.names?.[0] ?? assetInfo.name ?? '').toLowerCase();
  const assetPaths = [
    ...(assetInfo.names ?? []),
    ...(assetInfo.originalFileNames ?? []),
    assetInfo.name ?? '',
  ];
  const normalizedAssetPath = assetPaths.join('/').replaceAll('\\', '/');

  if (ext === '.css') {
    return 'css/[name][extname]';
  }

  if (['.woff', '.woff2', '.ttf', '.otf', '.eot'].includes(ext)) {
    return 'fonts/[name][extname]';
  }

  if (ext === '.svg' && normalizedAssetPath.includes('/icons/')) {
    return 'img/icons/[name][extname]';
  }

  if (['.png', '.jpg', '.jpeg', '.gif', '.webp', '.avif', '.svg'].includes(ext)) {
    return 'img/image/[name][extname]';
  }

  return 'assets/[name][extname]';
}

export default defineConfig({
  plugins: [htmlPartialsPlugin(), htmlOutputPlugin()],
  root: 'src',
  base: './',
  publicDir: false,
  build: {
    outDir: path.resolve(__dirname, 'dist'),
    emptyOutDir: true,
    manifest: true,
    cssCodeSplit: true,
    assetsInlineLimit: 0,
    rollupOptions: {
      input: collectPageInputs(),
      output: {
        // One shared chunk for code imported by 2+ pages (header/footer/utilities).
        manualChunks(id) {
          if (id.includes('/src/common.')) return 'common';
          if (id.includes('/src/styles/')) return 'common';
          if (id.includes('/src/js/core/')) return 'common';
          // Only components imported from common.js roll into the shared chunk.
          if (id.includes('/src/js/components/') && !id.includes('accordion.js')) return 'common';
          return undefined;
        },
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        assetFileNames: getAssetFileName,
      },
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
      },
    },
  },
  server: {
    port: 5173,
    strictPort: false,
  },
});
