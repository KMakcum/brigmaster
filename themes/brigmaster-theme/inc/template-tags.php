<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Breadcrumbs are owned by Rank Math. If the plugin is unavailable, render nothing.
 */
function bm_breadcrumbs(?WP_Post $post = null): void
{
    if (is_front_page()) {
        return;
    }

    if (function_exists('rank_math_the_breadcrumbs')) {
        rank_math_the_breadcrumbs();

        return;
    }
}

/**
 * Approximate reading time for archive cards (news list), in full minutes (min 1).
 */
function bm_reading_time_minutes(?int $post_id = null): int
{
    $p = get_post($post_id);
    if (!$p instanceof WP_Post) {
        return 1;
    }

    $plain = wp_strip_all_tags((string) $p->post_content);
    if ($plain === '') {
        return 1;
    }

    $tokens = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
    $n = is_array($tokens) ? count($tokens) : 0;

    return max(1, (int) ceil($n / 200));
}

/**
 * Russian copy for "found N posts" (list header), matching news archive UX.
 */
function bm_archive_found_posts_label(int $count): string
{
    $n = absint($count);
    $mod10 = $n % 10;
    $mod100 = $n % 100;

    if ($mod10 === 1 && $mod100 !== 11) {
        return sprintf(
            /* translators: %d: post count */
            __('Найдена %d статья', 'brigmaster-theme'),
            $n
        );
    }

    if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
        return sprintf(
            /* translators: %d: post count */
            __('Найдено %d статьи', 'brigmaster-theme'),
            $n
        );
    }

    return sprintf(
        /* translators: %d: post count */
        __('Найдено %d статей', 'brigmaster-theme'),
        $n
    );
}
