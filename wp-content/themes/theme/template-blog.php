<?php
/*
Template Name: Page Blog
*/
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
    'format' => 'option'
);
$months = wp_get_archives($args);
//$pattern = ' ((19|20)\d{2}( </option>))';
//$months = preg_replace($pattern, '\\3', $months);
//$months=array("January", "February", "March" , "April" , "May" , "June" , "July" , "August" , "September" , "October" , "November" , "December");

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

Timber::render('pages/category.twig', $context );
