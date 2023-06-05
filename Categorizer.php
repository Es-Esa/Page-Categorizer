<?php
/**
 * Plugin Name: Page Categorizer
 * Description: Allows administrators to categorize pages using custom categories.
 * Version: 1.0
 * Author: Henrik Viljanen henrikviljanen.fi
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 */


// Create custom taxonomy for page categories
function page_categorizer_register_taxonomy() {
    $labels = array(
        'name'              => _x( 'Page Categories', 'taxonomy general name', 'page-categorizer' ),
        'singular_name'     => _x( 'Page Category', 'taxonomy singular name', 'page-categorizer' ),
        'search_items'      => __( 'Search Page Categories', 'page-categorizer' ),
        'all_items'         => __( 'All Page Categories', 'page-categorizer' ),
        'parent_item'       => __( 'Parent Page Category', 'page-categorizer' ),
        'parent_item_colon' => __( 'Parent Page Category:', 'page-categorizer' ),
        'edit_item'         => __( 'Edit Page Category', 'page-categorizer' ),
        'update_item'       => __( 'Update Page Category', 'page-categorizer' ),
        'add_new_item'      => __( 'Add New Page Category', 'page-categorizer' ),
        'new_item_name'     => __( 'New Page Category Name', 'page-categorizer' ),
        'menu_name'         => __( 'Page Categories', 'page-categorizer' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'page-category' ),
    );

    register_taxonomy( 'page-category', 'page', $args );
}
add_action( 'init', 'page_categorizer_register_taxonomy' );

// Add category metabox to page editor
function page_categorizer_add_category_metabox() {
    add_meta_box(
        'page-category-metabox',
        'Page Category',
        'page_categorizer_render_category_metabox',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'page_categorizer_add_category_metabox' );

// Render category metabox
function page_categorizer_render_category_metabox( $post ) {
    $taxonomy = 'page-category';
    $terms    = wp_get_object_terms( $post->ID, $taxonomy );
    $selected = ! empty( $terms ) ? $terms[0]->term_id : 0;

    $categories = get_terms( array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ) );
    ?>
    <div class="categorydiv">
        <div class="tabs-panel">
            <ul class="categorychecklist">
                <?php foreach ( $categories as $category ) : ?>
                    <li>
                        <label class="selectit">
                            <input type="radio" name="tax_input[<?php echo $taxonomy; ?>][]" value="<?php echo $category->term_id; ?>" <?php checked( $selected, $category->term_id ); ?>>
                            <?php echo $category->name; ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
}

// Save category data when page is saved or updated
function page_categorizer_save_category_data( $post_id ) {
    if ( isset( $_POST['tax_input'] ) ) {
        $taxonomy = 'page-category';
        $category = $_POST['tax_input'][ $taxonomy ][0];

        wp_set_object_terms( $post_id, intval( $category ), $taxonomy );
    }
}
add_action( 'save_post', 'page_categorizer_save_category_data' );
