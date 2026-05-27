<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

interface Constructly_Core_Page_Migration_Interface
{
    public static function page_key(): string;

    public static function resolve_target_page(): ?WP_Post;

    public static function build_content(): string;

    public static function migration_version(): string;

    /**
     * @return array{title:string, description:string}
     */
    public static function seo_meta(): array;
}
