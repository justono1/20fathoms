<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$templates = array( 'pages/archive.twig', 'pages/index.twig' );

$data = Timber::get_context();
$post = Timber::query_post();
$data['post'] = $post;
// $query = array();
// $data['title'] = 'Archive';
// if ( is_day() ) {
// 	$data['title'] = 'Archive: '.get_the_date( 'D M Y' );
// 	$query['day'] = get_the_date( 'D' ); $data['day'] = get_the_date( 'D' );
// 	$query['monthnum'] = get_the_date( 'M' ); $data['month'] = get_the_date( 'M' );
// 	$query['year'] = get_the_date( 'Y' ); $data['year'] = get_the_date( 'Y' );
// } else if ( is_month() ) {
//
// 	$data['title'] = 'Archive: '.get_the_date( 'M Y' );
// 	$query['monthnum'] = get_the_date( 'M' ); $data['month'] = get_the_date( 'M' );
// 	$query['year'] = get_the_date( 'Y' ); $data['year'] = get_the_date( 'Y' );
// } else if ( is_year() ) {
// 	$data['title'] = 'Archive: '.get_the_date( 'Y' );
// 	$query['year'] = get_the_date( 'Y' ); $data['year'] = get_the_date( 'Y' );
// } else if ( is_tag() ) {
// 	$data['title'] = single_tag_title( '', false );
// } else if ( is_category() ) {
// 	$data['title'] = single_cat_title( '', false );
// 	array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
// } else if ( is_post_type_archive() ) {
// 	$data['title'] = post_type_archive_title( '', false );
// 	array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
// }

foreach ( (array) $wpdb->get_results( "SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE " . get_private_posts_cap_sql( 'post' ) . " GROUP BY post_author" ) as $row ) {
    $author_count[] = get_userdata($row->post_author);
}

$args = array(
	'type'            => 'monthly',
	'echo'            => 0,
	'year'       =>  get_the_date( 'Y' ),
	'format' => 'option'
);
$months = wp_get_archives($args);
$pattern = ' ((19|20)\d{2}( </option>))';
$months = preg_replace($pattern, '\\3', $months);

$authors = get_users(array('orderby'=>'post_count'));
$years = wp_get_archives( array('type' => 'yearly','echo'=>false, 'format' => 'option') );

$blog_url = get_permalink(540);

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$cat = get_query_var('cat');
$currentCat = get_category($cat);
// $query['post_type'] = 'post';
// $posts = Timber::get_posts($query);
// $data['posts'] = $posts;
$data['paged'] = $paged;
$data['currentCat'] = $currentCat;
$data['cats'] = Timber::get_terms('category');
$data['postType'] = 'post';
$data['authors'] = $authors;
$data['years'] = $years;
$data['months'] = 'hey';
$data['blog_url'] = $blog_url;
$data['lp'] = true;
$data['authorcount'] = $author_count;

Timber::render( $templates, $data );
