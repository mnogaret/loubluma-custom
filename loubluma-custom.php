<?php
/**
 * Plugin Name: Loubluma Custom
 * Description: Custom plugin for loubluma.com.
 * Author: Mathieu Nogaret
 * Version: 0.1.0
 */

require __DIR__ . '/tooltipy-glossary-patch.php';
require __DIR__ . '/whitespace-patch.php';
require __DIR__ . '/help-popup-patch.php';

add_filter( 'astra_tablet_breakpoint', function () {
    return 1080;
});

// Ne garder que les pages dans le sitemap (retire posts + CPT + taxonomies + users).
add_filter('wp_sitemaps_post_types', function ($post_types) {
    return [
        'page' => $post_types['page'],
    ];
});
add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
    return [];
});
add_filter('wp_sitemaps_users', '__return_empty_array');
add_filter('wp_sitemaps_providers', function ($providers) {
    // Ne garder que le provider "posts" (qui contiendra uniquement 'page' grâce à ton filtre wp_sitemaps_post_types).
    return array_intersect_key($providers, ['posts' => true]);
});


