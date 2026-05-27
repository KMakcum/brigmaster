<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class Constructly_Content_Cli
{
    public static function init(): void
    {
        if (!defined('WP_CLI') || !WP_CLI) {
            return;
        }

        WP_CLI::add_command('constructly homepage migrate', [self::class, 'migrate_homepage']);
        WP_CLI::add_command('constructly phase4 migrate', [self::class, 'migrate_phase4']);
        WP_CLI::add_command('constructly foundation-hub migrate', [self::class, 'migrate_foundation_hub']);
        WP_CLI::add_command('constructly core-pages migrate', [self::class, 'migrate_core_pages']);
        WP_CLI::add_command('constructly content migrate', [self::class, 'migrate_content']);
        WP_CLI::add_command('constructly faq normalize', [self::class, 'normalize_faq']);
    }

    /**
     * Runs content migrations by group or by a single core page key.
     *
     * ## OPTIONS
     *
     * [--group=<group>]
     * : One of: calculators, trust, core, main, all.
     *
     * [--page=<key>]
     * : One of: brick, screed, drywall, tile, about, contacts, methodology.
     *
     * [--homepage_id=<id>]
     * : Homepage page ID. Required for --group=main or --group=all.
     *
     * [--foundation_hub_id=<id>]
     * : Foundation hub page ID. Required for --group=main or --group=all.
     *
     * ## EXAMPLES
     *
     *     wp constructly content migrate --group=calculators
     *     wp constructly content migrate --group=all --homepage_id=28 --foundation_hub_id=80
     *     wp constructly content migrate --page=brick
     *
     * @param array<int, string> $args
     * @param array<string, string|bool> $assoc_args
     */
    public static function migrate_content(array $args, array $assoc_args): void
    {
        $page_key = isset($assoc_args['page']) ? trim((string) $assoc_args['page']) : '';
        if ($page_key !== '') {
            self::migrate_core_page_keys([$page_key]);
            return;
        }

        $group = isset($assoc_args['group']) ? strtolower(trim((string) $assoc_args['group'])) : 'all';
        $updated = [];

        if (in_array($group, ['main', 'all'], true)) {
            $homepage_id = isset($assoc_args['homepage_id']) ? (int) $assoc_args['homepage_id'] : 0;
            $foundation_hub_id = isset($assoc_args['foundation_hub_id']) ? (int) $assoc_args['foundation_hub_id'] : 0;

            if ($homepage_id <= 0 || $foundation_hub_id <= 0) {
                WP_CLI::error('Groups "main" and "all" require --homepage_id and --foundation_hub_id.');
            }

            $homepage = Constructly_Content_Migrations::migrate_homepage($homepage_id);
            $foundation_hub = Constructly_Content_Migrations::migrate_foundation_hub_page($foundation_hub_id);
            $updated[] = sprintf('homepage:%d', $homepage['post_id']);
            $updated[] = sprintf('foundation-hub:%d', $foundation_hub['post_id']);
        }

        if ($group === 'main') {
            WP_CLI::success('Content migration updated: ' . implode(', ', $updated) . '.');
            return;
        }

        $core_group = $group === 'all' ? 'core' : $group;
        $keys = Constructly_Content_Migrations::core_page_group_keys($core_group);
        if ($keys === []) {
            WP_CLI::error('Unsupported content migration group.');
        }

        $updated = array_merge($updated, self::migrate_core_page_keys($keys, false));
        if ($group === 'all') {
            $faq_updated = self::normalize_faq_pages(false);
            foreach ($faq_updated as $page_id) {
                $updated[] = sprintf('faq:%d', $page_id);
            }
        }

        WP_CLI::success('Content migration updated: ' . implode(', ', $updated) . '.');
    }

    /**
     * @param list<string> $keys
     * @return list<string>
     */
    private static function migrate_core_page_keys(array $keys, bool $print_success = true): array
    {
        $updated = [];

        foreach ($keys as $key) {
            $rows = Constructly_Content_Migrations::migrate_core_pages($key);
            foreach ($rows as $row) {
                $updated[] = sprintf('%s:%d', $row['page_key'], $row['post_id']);
            }
        }

        if ($print_success) {
            if ($updated === []) {
                WP_CLI::success('No content pages were updated.');
                return [];
            }

            WP_CLI::success('Content migration updated: ' . implode(', ', $updated) . '.');
        }

        return $updated;
    }

    /**
     * @return list<int>
     */
    private static function normalize_faq_pages(bool $dry_run = false): array
    {
        $pages = get_posts([
            'post_type' => 'page',
            'post_status' => ['publish', 'draft', 'private'],
            'numberposts' => -1,
        ]);

        $changed = [];

        foreach ($pages as $page) {
            if (!$page instanceof WP_Post) {
                continue;
            }

            $original = (string) $page->post_content;
            if (!str_contains($original, 'rank-math/faq-block')) {
                continue;
            }

            $next = Constructly_Content_Migrations::normalize_faq_content_for_storage($original);
            if ($next === $original) {
                continue;
            }

            if (!$dry_run) {
                wp_update_post([
                    'ID' => $page->ID,
                    'post_content' => $next,
                ]);
            }

            $changed[] = (int) $page->ID;
        }

        return $changed;
    }

    /**
     * ## OPTIONS
     *
     * --page_id=<id>
     * : Existing homepage page ID.
     *
     * [--dry-run]
     * : Print generated content without writing to the database.
     *
     * ## EXAMPLES
     *
     *     wp constructly homepage migrate --page_id=123
     *     wp constructly homepage migrate --page_id=123 --dry-run
     *
     * @param array<int, string> $args
     * @param array<string, string|bool> $assoc_args
     */
    public static function migrate_homepage(array $args, array $assoc_args): void
    {
        $page_id = isset($assoc_args['page_id']) ? (int) $assoc_args['page_id'] : 0;

        if ($page_id <= 0) {
            WP_CLI::error('Missing required --page_id argument.');
        }

        if (!empty($assoc_args['dry-run'])) {
            WP_CLI::line(Constructly_Content_Migrations::build_homepage_content());
            WP_CLI::success('Dry run completed.');

            return;
        }

        $result = Constructly_Content_Migrations::migrate_homepage($page_id);

        WP_CLI::success(
            sprintf(
                'Homepage content migrated for page ID %d using %s.',
                $result['post_id'],
                $result['migration']
            )
        );
    }

    /**
     * Replaces [brigmaster_foundation_hub] with the constructly/foundation-hub block and adds title="" to estimator shortcodes.
     *
     * [--dry-run]
     * : Print transformed content per changed page without writing.
     *
     * ## EXAMPLES
     *
     *     wp constructly phase4 migrate
     *     wp constructly phase4 migrate --dry-run
     *
     * @param array<int, string> $args
     * @param array<string, string|bool> $assoc_args
     */
    public static function migrate_phase4(array $args, array $assoc_args): void
    {
        if (!empty($assoc_args['dry-run'])) {
            $pages = get_posts([
                'post_type' => 'page',
                'post_status' => ['publish', 'draft', 'private'],
                'numberposts' => -1,
            ]);

            foreach ($pages as $page) {
                if (!$page instanceof WP_Post) {
                    continue;
                }

                $original = (string) $page->post_content;
                $next = Constructly_Content_Migrations::transform_post_content_phase4($original);

                if ($next !== $original) {
                    WP_CLI::line(sprintf('--- Page ID %d ---', $page->ID));
                    WP_CLI::line($next);
                }
            }

            WP_CLI::success('Dry run completed.');

            return;
        }

        $updated = Constructly_Content_Migrations::migrate_phase4();

        if ($updated === []) {
            WP_CLI::success('No pages required Phase 4 content updates.');

            return;
        }

        WP_CLI::success(
            sprintf(
                'Phase 4 migration updated %d page(s): %s.',
                count($updated),
                implode(', ', $updated)
            )
        );
    }

    /**
     * Replaces the single constructly/foundation-hub block with editable sub-blocks + Rank Math FAQ.
     *
     * ## OPTIONS
     *
     * --page_id=<id>
     * : Page ID (e.g. «Калькулятор фундамента»).
     *
     * [--dry-run]
     * : Print generated block markup without saving.
     *
     * ## EXAMPLES
     *
     *     wp constructly foundation-hub migrate --page_id=123
     *     wp constructly foundation-hub migrate --page_id=123 --dry-run
     *
     * @param array<int, string> $args
     * @param array<string, string|bool> $assoc_args
     */
    public static function migrate_foundation_hub(array $args, array $assoc_args): void
    {
        $page_id = isset($assoc_args['page_id']) ? (int) $assoc_args['page_id'] : 0;

        if ($page_id <= 0) {
            WP_CLI::error('Missing required --page_id argument.');
        }

        if (!empty($assoc_args['dry-run'])) {
            WP_CLI::line(Constructly_Content_Migrations::build_foundation_hub_page_content());
            WP_CLI::success('Dry run completed.');

            return;
        }

        $result = Constructly_Content_Migrations::migrate_foundation_hub_page($page_id);

        WP_CLI::success(
            sprintf(
                'Foundation hub content migrated for page ID %d using %s.',
                $result['post_id'],
                $result['migration']
            )
        );
    }

    /**
     * Rebuilds core money/trust pages from migration source so content survives future reruns.
     *
     * ## OPTIONS
     *
     * [--page=<key>]
     * : One of: brick, screed, drywall, tile, about, contacts, methodology.
     *
     * [--dry-run]
     * : Print generated block markup without saving.
     *
     * ## EXAMPLES
     *
     *     wp constructly core-pages migrate
     *     wp constructly core-pages migrate --page=tile
     *     wp constructly core-pages migrate --page=contacts --dry-run
     *
     * @param array<int, string> $args
     * @param array<string, string|bool> $assoc_args
     */
    public static function migrate_core_pages(array $args, array $assoc_args): void
    {
        $page_key = isset($assoc_args['page']) ? trim((string) $assoc_args['page']) : '';
        $page_key = $page_key !== '' ? $page_key : null;

        if (!empty($assoc_args['dry-run'])) {
            if ($page_key !== null) {
                WP_CLI::line(Constructly_Content_Migrations::build_core_page_content($page_key));
                WP_CLI::success('Dry run completed.');

                return;
            }

            foreach (Constructly_Core_Pages_Migration::supported_page_keys() as $key) {
                WP_CLI::line(sprintf('--- %s ---', $key));
                WP_CLI::line(Constructly_Content_Migrations::build_core_page_content($key));
                WP_CLI::line('');
            }

            WP_CLI::success('Dry run completed.');

            return;
        }

        $updated = Constructly_Content_Migrations::migrate_core_pages($page_key);

        if ($updated === []) {
            WP_CLI::success('No core pages were updated.');

            return;
        }

        $summary = array_map(
            static fn (array $row): string => sprintf('%s:%d', $row['page_key'], $row['post_id']),
            $updated
        );

        WP_CLI::success(
            sprintf('Core page migration updated %d page(s): %s.', count($updated), implode(', ', $summary))
        );
    }

    /**
     * Re-serializes Rank Math FAQ blocks (valid inner HTML) and inserts the standard H2 when missing.
     *
     * [--dry-run]
     * : List page IDs that would change without saving.
     *
     * ## EXAMPLES
     *
     *     wp constructly faq normalize
     *     wp constructly faq normalize --dry-run
     *
     * @param array<int, string> $args
     * @param array<string, string|bool> $assoc_args
     */
    public static function normalize_faq(array $args, array $assoc_args): void
    {
        if (!empty($assoc_args['dry-run'])) {
            $changed = self::normalize_faq_pages(true);
            foreach ($changed as $page_id) {
                WP_CLI::line(sprintf('Would update page ID %d', $page_id));
            }
            WP_CLI::success('Dry run completed.');

            return;
        }

        $changed = self::normalize_faq_pages(false);

        if ($changed === []) {
            WP_CLI::success('No pages required FAQ normalization.');

            return;
        }

        WP_CLI::success(
            sprintf('FAQ normalization updated %d page(s): %s.', count($changed), implode(', ', $changed))
        );
    }
}
