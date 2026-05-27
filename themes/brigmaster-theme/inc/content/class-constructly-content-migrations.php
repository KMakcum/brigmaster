<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Content_Migrations
{
    public const FAQ_SECTION_TITLE = Constructly_Rank_Math_Faq_Migration::FAQ_SECTION_TITLE;
    private const CORE_PAGE_GROUPS = [
        'calculators' => ['brick', 'screed', 'drywall', 'tile'],
        'trust' => ['about', 'contacts', 'methodology'],
        'core' => ['brick', 'screed', 'drywall', 'tile', 'about', 'contacts', 'methodology'],
    ];

    public static function init(): void
    {
    }

    /**
     * @return array{post_id:int, content:string, migration:string}
     */
    public static function migrate_homepage(int $page_id): array
    {
        return Constructly_Homepage_Migration::migrate_homepage($page_id);
    }

    public static function build_homepage_content(): string
    {
        return Constructly_Homepage_Migration::build_homepage_content();
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    public static function merge_rank_math_faq_default_attributes(array $attributes): array
    {
        return Constructly_Rank_Math_Faq_Migration::merge_default_attributes($attributes);
    }

    /**
     * Saved markup for Rank Math FAQ (must match block save.js) so the editor validates and RichText fields stay clean.
     *
     * @param array<string, mixed> $attributes
     */
    public static function rank_math_faq_saved_inner_html(array $attributes): string
    {
        return Constructly_Rank_Math_Faq_Migration::saved_inner_html($attributes);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public static function serialize_rank_math_faq_block(array $attributes): string
    {
        return Constructly_Rank_Math_Faq_Migration::serialize_block($attributes);
    }

    public static function faq_section_heading_block(): string
    {
        return Constructly_Rank_Math_Faq_Migration::section_heading_block();
    }

    public static function normalize_rank_math_faq_in_content(string $content): string
    {
        return Constructly_Rank_Math_Faq_Migration::normalize_rank_math_faq_in_content($content);
    }

    public static function ensure_faq_section_heading_in_content(string $content): string
    {
        return Constructly_Rank_Math_Faq_Migration::ensure_faq_section_heading_in_content($content);
    }

    /**
     * Fixes Rank Math FAQ serialization and ensures the standard FAQ section heading exists before the block.
     */
    public static function normalize_faq_content_for_storage(string $content): string
    {
        return Constructly_Rank_Math_Faq_Migration::normalize_content_for_storage($content);
    }

    /**
     * @return list<int>
     */
    public static function migrate_phase4(): array
    {
        return Constructly_Legacy_Content_Migration::migrate_legacy_content();
    }

    public static function transform_post_content_phase4(string $content): string
    {
        return Constructly_Legacy_Content_Migration::transform_legacy_content($content);
    }

    /**
     * @return array{post_id:int, content:string, migration:string}
     */
    public static function migrate_foundation_hub_page(int $page_id): array
    {
        return Constructly_Foundation_Hub_Migration::migrate_foundation_hub_page($page_id);
    }

    public static function build_foundation_hub_page_content(): string
    {
        return Constructly_Foundation_Hub_Migration::build_foundation_hub_page_content();
    }

    public static function replace_foundation_hub_mono_block_with_sections(string $content): string
    {
        return Constructly_Foundation_Hub_Migration::replace_foundation_hub_mono_block_with_sections($content);
    }

    /**
     * @param ?string $page_key
     * @return list<array{page_key:string, post_id:int, content:string, migration:string}>
     */
    public static function migrate_core_pages(?string $page_key = null): array
    {
        return Constructly_Core_Pages_Migration::migrate_pages($page_key);
    }

    /**
     * @return list<string>
     */
    public static function core_page_group_keys(string $group): array
    {
        $group = strtolower(trim($group));

        return self::CORE_PAGE_GROUPS[$group] ?? [];
    }

    public static function build_core_page_content(string $page_key): string
    {
        return Constructly_Core_Pages_Migration::build_page_content($page_key);
    }
}
