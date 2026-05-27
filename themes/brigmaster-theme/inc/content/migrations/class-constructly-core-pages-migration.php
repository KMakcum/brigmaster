<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Core_Pages_Migration
{
    /**
     * @var array<string, class-string<Constructly_Core_Page_Migration_Interface>>
     */
    private const REGISTRY = [
        'brick' => Constructly_Brick_Core_Page_Migration::class,
        'screed' => Constructly_Screed_Core_Page_Migration::class,
        'drywall' => Constructly_Drywall_Core_Page_Migration::class,
        'tile' => Constructly_Tile_Core_Page_Migration::class,
        'about' => Constructly_About_Core_Page_Migration::class,
        'contacts' => Constructly_Contacts_Core_Page_Migration::class,
        'methodology' => Constructly_Methodology_Core_Page_Migration::class,
    ];

    /**
     * @return list<string>
     */
    public static function supported_page_keys(): array
    {
        return array_keys(self::REGISTRY);
    }

    /**
     * @param ?string $page_key
     * @return list<array{page_key:string, post_id:int, content:string, migration:string}>
     */
    public static function migrate_pages(?string $page_key = null): array
    {
        $keys = $page_key !== null ? [self::normalize_page_key($page_key)] : self::supported_page_keys();
        $updated = [];

        foreach ($keys as $key) {
            $class = self::REGISTRY[$key];
            $page = $class::resolve_target_page();
            if (!$page instanceof WP_Post) {
                continue;
            }

            $content = $class::build_content();
            $migration = $class::migration_version();

            wp_update_post([
                'ID' => $page->ID,
                'post_content' => $content,
            ]);

            update_post_meta($page->ID, '_constructly_content_migration', $migration);
            self::update_seo_meta($page->ID, $key);

            $updated[] = [
                'page_key' => $key,
                'post_id' => (int) $page->ID,
                'content' => $content,
                'migration' => $migration,
            ];
        }

        return $updated;
    }

    public static function build_page_content(string $page_key): string
    {
        $class = self::definition_class(self::normalize_page_key($page_key));

        return $class::build_content();
    }

    /**
     * @return class-string<Constructly_Core_Page_Migration_Interface>
     */
    private static function definition_class(string $normalized_key): string
    {
        if (!isset(self::REGISTRY[$normalized_key])) {
            throw new InvalidArgumentException('Unsupported core page key.');
        }

        return self::REGISTRY[$normalized_key];
    }

    private static function update_seo_meta(int $page_id, string $page_key): void
    {
        $class = self::definition_class($page_key);
        $meta = $class::seo_meta();
        update_post_meta($page_id, 'rank_math_title', $meta['title']);
        update_post_meta($page_id, 'rank_math_description', $meta['description']);
    }

    private static function normalize_page_key(string $page_key): string
    {
        $key = strtolower(trim($page_key));
        if (!isset(self::REGISTRY[$key])) {
            throw new InvalidArgumentException('Unsupported core page key.');
        }

        return $key;
    }
}
