<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Vite output: {@see assets/dist/.vite/manifest.json}
 */
final class Constructly_Assets
{
    private const MANIFEST_RELATIVE = '/assets/dist/.vite/manifest.json';

    private const ENTRY_MAIN = 'src/main.js';

    private const ENTRY_EDITOR = 'src/editor.js';

    private const ENTRY_BM_CUSTOM_SELECT = 'src/js/bm-custom-select.js';

    private const ENTRY_RANK_MATH_FAQ = 'src/js/rank-math-faq.js';

    /**
     * @var array<string, mixed>|null
     */
    private static ?array $manifest_cache = null;

    public static function init(): void
    {
        add_action('enqueue_block_editor_assets', [self::class, 'enqueue_block_editor_assets'], 20);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function get_manifest(): ?array
    {
        if (self::$manifest_cache !== null) {
            return self::$manifest_cache;
        }

        $path = CONSTRUCTLY_THEME_PATH . self::MANIFEST_RELATIVE;
        if (!is_readable($path)) {
            self::$manifest_cache = null;

            return null;
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        self::$manifest_cache = is_array($decoded) ? $decoded : null;

        return self::$manifest_cache;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function get_entry(string $entry_key): ?array
    {
        $manifest = self::get_manifest();
        if ($manifest === null) {
            return null;
        }

        $entry = $manifest[$entry_key] ?? null;

        return is_array($entry) ? $entry : null;
    }

    public static function enqueue_main_bundle(): void
    {
        $entry = self::get_entry(self::ENTRY_MAIN);
        if ($entry === null) {
            return;
        }

        $css_list = isset($entry['css']) && is_array($entry['css']) ? $entry['css'] : [];
        foreach ($css_list as $index => $relative) {
            if (!is_string($relative) || $relative === '') {
                continue;
            }
            $handle = $index === 0 ? 'bm-theme' : 'bm-theme-' . (string) $index;
            $abs_path = CONSTRUCTLY_THEME_PATH . '/assets/dist/' . $relative;
            wp_enqueue_style(
                $handle,
                CONSTRUCTLY_THEME_URL . '/assets/dist/' . $relative,
                [],
                is_readable($abs_path) ? (string) filemtime($abs_path) : CONSTRUCTLY_THEME_VERSION
            );
        }

        $script_relative = isset($entry['file']) && is_string($entry['file']) ? $entry['file'] : '';
        if ($script_relative !== '') {
            $script_path = CONSTRUCTLY_THEME_PATH . '/assets/dist/' . $script_relative;
            if (is_readable($script_path) && filesize($script_path) > 32) {
                wp_enqueue_script(
                    'bm-theme',
                    CONSTRUCTLY_THEME_URL . '/assets/dist/' . $script_relative,
                    [],
                    (string) filemtime($script_path),
                    true
                );
            }
        }
    }

    public static function enqueue_block_editor_assets(): void
    {
        $entry = self::get_entry(self::ENTRY_EDITOR);
        if ($entry === null) {
            return;
        }

        $css_list = isset($entry['css']) && is_array($entry['css']) ? $entry['css'] : [];
        foreach ($css_list as $index => $relative) {
            if (!is_string($relative) || $relative === '') {
                continue;
            }
            $handle = $index === 0 ? 'bm-theme-editor' : 'bm-theme-editor-' . (string) $index;
            $abs_path = CONSTRUCTLY_THEME_PATH . '/assets/dist/' . $relative;
            wp_enqueue_style(
                $handle,
                CONSTRUCTLY_THEME_URL . '/assets/dist/' . $relative,
                ['wp-edit-blocks'],
                is_readable($abs_path) ? (string) filemtime($abs_path) : CONSTRUCTLY_THEME_VERSION
            );
        }
    }

    /**
     * @param list<string> $deps
     */
    public static function enqueue_script_entry(string $entry_key, string $handle, array $deps, bool $in_footer): void
    {
        $entry = self::get_entry($entry_key);
        if ($entry === null) {
            return;
        }

        $relative = isset($entry['file']) && is_string($entry['file']) ? $entry['file'] : '';
        if ($relative === '') {
            return;
        }

        $abs_path = CONSTRUCTLY_THEME_PATH . '/assets/dist/' . $relative;
        if (!is_readable($abs_path)) {
            return;
        }

        wp_enqueue_script(
            $handle,
            CONSTRUCTLY_THEME_URL . '/assets/dist/' . $relative,
            $deps,
            (string) filemtime($abs_path),
            $in_footer
        );
    }

    /**
     * @return array{'src': non-falsy-string, 'ver': string}|null
     */
    public static function get_registered_script_bundle(string $entry_key): ?array
    {
        $entry = self::get_entry($entry_key);
        if ($entry === null) {
            return null;
        }

        $relative = isset($entry['file']) && is_string($entry['file']) ? $entry['file'] : '';
        if ($relative === '') {
            return null;
        }

        $abs_path = CONSTRUCTLY_THEME_PATH . '/assets/dist/' . $relative;
        if (!is_readable($abs_path)) {
            return null;
        }

        return [
            'src' => CONSTRUCTLY_THEME_URL . '/assets/dist/' . $relative,
            'ver' => (string) filemtime($abs_path),
        ];
    }

    /**
     * @return non-empty-string
     */
    public static function entry_key_bm_custom_select(): string
    {
        return self::ENTRY_BM_CUSTOM_SELECT;
    }

    /**
     * @return non-empty-string
     */
    public static function entry_key_rank_math_faq(): string
    {
        return self::ENTRY_RANK_MATH_FAQ;
    }
}
