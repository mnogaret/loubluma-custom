<?php
/**
 * Plugin Name: WP Custom Tooltipy Glossary
 * Description: Custom shortcode for filtering Tooltipy glossary by family/category.
 * Author: Mathieu Nogaret
 * Version: 0.1.0
 */

require __DIR__ . '/functions.php';

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', function() {
    register_taxonomy(
        'tooltipy_category',
        'my_keywords',
        [
            'label'        => 'Catégories du glossaire',
            'hierarchical' => true,
            'public'       => true,
            'show_ui'      => true,
            'show_in_rest' => true,
        ]
    );
});

function get_post_first_letter( $post_id ) {
    $custom = get_post_meta( $post_id, 'custom-index-first-letter', true );
    if ( is_string( $custom ) ) {
        $custom = trim( $custom );
    }
    $str = empty( $custom ) ? get_the_title( $post_id ) : $custom;
    $first = mb_substr( $str, 0, 1, 'UTF-8' );
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $first );
    if ( $ascii === false ) {
        $ascii = $first;
    }
    if ( empty( $ascii ) ) {
        $ascii = '#';
    }
    return strtoupper( $ascii );
}

function custom_tooltipy_glossary( $atts ) {
    $atts = shortcode_atts(
        [
            'cat'     => '',
            'orderby' => 'title',
            'order'   => 'ASC',
        ],
        $atts,
        'custom_tooltipy_glossary'
    );

    $glossary_options = get_option( 'bluet_glossary_options' );

    if ( !empty( $glossary_options['kttg_glossary_text']['kttg_glossary_text_all'] ) and $glossary_options['kttg_glossary_text']['kttg_glossary_text_all'] != "" ) {
        $text_all = $glossary_options['kttg_glossary_text']['kttg_glossary_text_all'];
    } else {
        $text_all = __('ALL','tooltipy-lang');
    }

    $tax_query = array();
    if ( $atts['cat'] !== '' ) {
        // $terms = array_map( 'trim', explode( ',', $atts['cat'] ) );
        $terms = esc_html( $atts['cat'] );
        $tax_query = [ [
            'taxonomy' => 'keywords_family',
            'field'    => 'slug',
            'terms'    => $terms,
        ] ];
    }

    $current_letter_class = '';
    $chosen_letter = null;
    if ( !empty ( $_GET['letter'] ) and $_GET['letter'] ) {
        $chosen_letter = sanitize_text_field( $_GET["letter"] );
    }
    if ( $chosen_letter == null ) {
        $current_letter_class = 'bluet_glossary_current_letter';
    }

    $permalink = get_permalink();

    $args = [
        'post_type'      => 'my_keywords',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ];

    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    $q = new WP_Query( $args );

    ob_start();
    echo '<div class="kttg_glossary_div">';

    // Si aucun résultat : on peut terminer rapidement
    if ( ! $q->have_posts() ) {
        ob_start();
        echo '<p>Aucun terme trouvé.</p>';
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    // Construction des lettres
    echo '<div class="kttg_glossary_header"><span class="bluet_glossary_all ' . $current_letter_class . '"><a href=\'' . $permalink . '\'>' . $text_all . '</a></span> - ';

    $chars_count = [];
    $posts_by_letter = [];
    while ( $q->have_posts() ) {
        $q->the_post();
        $post_id = get_the_ID();
        $first_letter = get_post_first_letter( $post_id );
        if ( empty( $chars_count[ $first_letter ] ) ) {
            $chars_count[ $first_letter ] = 0;
            $posts_by_letter[ $first_letter ] = [];
        }
        $chars_count[ $first_letter ]++;
        $posts_by_letter[ $first_letter ][] = $post_id;
    }
    wp_reset_postdata();

    ksort( $chars_count );
    ksort( $posts_by_letter );

    // Menu

    foreach ( $chars_count as $my_char => $nb ) {
        $current_class = '';
        $glossary_page_url = $permalink;
        $link_to_the_letter_page = add_query_arg( 'letter', $my_char, $glossary_page_url );
        if ( $chosen_letter == $my_char ) {
            $current_class = 'bluet_glossary_current_letter';
        }
        echo ' <span class="bluet_glossary_letter bluet_glossary_found_letter ' . $current_class . '"><a href=\'' . $link_to_the_letter_page . '\'>' . esc_html( $my_char ) . '<span class="bluet_glossary_letter_count">' . intval( $nb ) . '</span></a></span>';
    }
    echo '</div>';

    // Contenu

    echo '<div class="custom_glossary_content" style="margin-top: 20px;">';
    echo '<dl class="wp-custom-tooltipy-glossary">';

    foreach ( $chars_count as $current_letter => $nb ) {
        if ( $chosen_letter !== null && $current_letter !== $chosen_letter ) {
            continue;
        }
        echo '<h1>— ' . $current_letter . ' —</h1>';
        if ( empty( $posts_by_letter[ $current_letter ] ) ) {
            continue;
        }

        foreach ( $posts_by_letter[ $current_letter ] as $post_id ) {
            $post_title = get_the_title( $post_id );
            $slug = get_post_field( 'post_name', $post_id );

            echo '<dt id="glossary-' . esc_attr( $slug ) . '">';
            echo '<h2 class="glossary_element_title">';
            echo '<span class="no-tooltipy">' . esc_html( $post_title ) . '</span>';

            if ( current_user_can( 'edit_post', $post_id ) ) {
                $edit_link = get_edit_post_link( $post_id );
                echo '&nbsp;<small>–&nbsp;<a href="' . esc_url( $edit_link ) . '">modifier</a></small>';
            }

            echo '</h2>';
            echo '</dt>';
            echo '<dd>' . wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ) ) . '</dd>';
        }
    }

    echo '</dl>';
    echo '</div>';
    wp_reset_postdata();

    echo '</div>';

    return ob_get_clean();
}
add_shortcode( 'custom_tooltipy_glossary', 'custom_tooltipy_glossary' );

