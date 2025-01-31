<?php
/**
 * Use multiple sku's to find WOO products in wp-admin
 * NOTE: Use '|' as a sku delimiter in your search query. Example: '1234|1235|1236'
**/
function woo_multiple_sku_search( $query_vars ) {

global $typenow;
global $wpdb;
global $pagenow;

if ( 'product' === $typenow && isset( $_GET['s'] ) && 'edit.php' === $pagenow ) {
    $search_term = esc_sql( sanitize_text_field( $_GET['s'] ) );

    if (strpos($search_term, '|') == false) return $query_vars;

    $skus = explode('|',$search_term);

    $meta_query = array(
        'relation' => 'OR'
    );
    if(is_array($skus) && $skus) {
        foreach($skus as $sku) {
            $meta_query[] = array(
                'key' => '_sku',
                'value' => $sku,
                'compare' => '='
            );
        }
    }

    $args = array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'meta_query'      => $meta_query
    );
    $posts = get_posts( $args );

    if ( ! $posts ) return $query_vars;

    foreach($posts as $post){
      $query_vars['post__in'][] = $post->ID;
    }
}

return $query_vars;
}
add_filter( 'request', 'woo_multiple_sku_search', 20 );
?>
