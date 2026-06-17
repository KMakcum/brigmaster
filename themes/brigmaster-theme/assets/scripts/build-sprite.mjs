/**
 * Builds a single SVG sprite from individual files in src/icons/sprite/*.svg.
 * Each file becomes a <symbol id="bm-icon-<filename>"> inside the sprite.
 * Output: src/icons/_sprite.svg
 *
 * The sprite is then embedded into HTML via <?php include_once ... ?> in WP
 * or via inline JS fetch + insert at runtime. Usage:
 *   <svg class="bm-icon"><use href="#bm-icon-chevron-down"/></svg>
 */
import { readdirSync, readFileSync, writeFileSync, existsSync, mkdirSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const SRC_DIR = path.resolve(__dirname, '../src/icons/sprite');
const OUT_FILE = path.resolve(__dirname, '../src/icons/_sprite.svg');

function toSymbol(filePath, fileName) {
  const raw = readFileSync(filePath, 'utf8');

  // Extract viewBox (required)
  const viewBoxMatch = raw.match(/viewBox="([^"]+)"/i);
  if (!viewBoxMatch) {
    console.warn(`[sprite] WARN: ${fileName} has no viewBox, skipped.`);
    return null;
  }
  const viewBox = viewBoxMatch[1];

  // Strip outer <svg ...> ... </svg> and grab inner content.
  const inner = raw
    .replace(/<\?xml[^>]*>/g, '')
    .replace(/<!DOCTYPE[^>]*>/g, '')
    .replace(/<svg[^>]*>/i, '')
    .replace(/<\/svg>/i, '')
    .trim();

  const id = `bm-icon-${path.basename(fileName, '.svg')}`;
  return `  <symbol id="${id}" viewBox="${viewBox}">${inner}</symbol>`;
}

function build() {
  if (!existsSync(SRC_DIR)) {
    mkdirSync(SRC_DIR, { recursive: true });
  }
  const files = readdirSync(SRC_DIR)
    .filter((f) => f.toLowerCase().endsWith('.svg'))
    .sort();

  const symbols = files
    .map((f) => toSymbol(path.join(SRC_DIR, f), f))
    .filter(Boolean);

  const sprite = `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">
${symbols.join('\n')}
</svg>
`;

  writeFileSync(OUT_FILE, sprite, 'utf8');
  console.log(`[sprite] built ${symbols.length} icons -> ${path.relative(process.cwd(), OUT_FILE)}`);
}

build();
