/**
 * Component bootstrap.
 *
 * Components are wired up declaratively: any element with a
 * `data-bm-component="name"` attribute gets matched against a registered
 * initializer. Register components elsewhere via `registerComponent('name', fn)`.
 *
 * This lets pages opt into behavior without each page importing each
 * component module imperatively.
 */

const registry = new Map();

export function registerComponent(name, initFn) {
  if (registry.has(name)) {
    console.warn(`[bm] component "${name}" already registered, overwriting.`);
  }
  registry.set(name, initFn);
}

export function bootstrap(root = document) {
  for (const [name, initFn] of registry) {
    const nodes = root.querySelectorAll(`[data-bm-component="${name}"]`);
    nodes.forEach((node) => {
      if (node.dataset.bmInitialized === '1') return;
      try {
        initFn(node);
        node.dataset.bmInitialized = '1';
      } catch (err) {
        console.error(`[bm] failed to init "${name}"`, err);
      }
    });
  }
}
