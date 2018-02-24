<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */
// $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$context = Timber::get_context();
$context['posts'] = Timber::get_posts();

$authors = get_users(array('orderby'=>'post_count'));
$years = wp_get_archives( array('type' => 'yearly','echo'=>false, 'format' => 'option') );

$author_count = array();
foreach ( (array) $wpdb->get_results( "SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author" ) as $row ) {
    $author_count[] = get_userdata($row->post_author);
}

//print_r($author_count); exit;

$args = array(
    'type'            => 'monthly',
    'echo'            => 0,
//    'year'       =>  date('Y'),
    'format' => 'html'
);
$months = wp_get_archives($args);

$blog_url = get_permalink(540);

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$cat = get_query_var('cat');
$currentCat = get_category($cat);

$context = Timber::get_context();
$posts = Timber::get_posts(array('post_type' => 'post'));
$context['posts'] = $posts;
$context['paged'] = $paged;
$context['currentCat'] = $currentCat;
$context['cats'] = Timber::get_terms('category');
$context['postType'] = 'post';
$context['authors'] = $authors;
$context['years'] = $years;
$context['months'] = $months;
$context['blog_url'] = $blog_url;
$context['year'] = date('Y');
$context['lp'] = true;
$context['authorcount'] = $author_count;

$templates = array( 'pages/feed.twig' );

Timber::render( $templates, $context );
