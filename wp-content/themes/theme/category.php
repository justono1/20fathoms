<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$authors = get_users(array('orderby'=>'post_count'));
$years = wp_get_archives( array('type' => 'yearly','echo'=>false, 'format' => 'option') );
//$months=array("January", "February", "March" , "April" , "May" , "June" , "July" , "August" , "September" , "October" , "November" , "December");
$args = array(
    'type'            => 'monthly',
    'echo'            => 0,
//    'year'       =>  date( 'Y' ),
    'format' => 'option'
);
$months = wp_get_archives($args);
//$pattern = ' ((19|20)\d{2}( </option>))';
//$months = preg_replace($pattern, '\\3', $months);
$blog_url = get_permalink(540);

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
$cat = get_query_var('cat');
$currentCat = get_category($cat);

$context = Timber::get_context();
$posts = Timber::get_posts(array('category_name' => $currentCat->slug, 'paged'=>$paged));
$context['posts'] = $posts;
$context['paged'] = $paged;
$context['currentCat'] = $currentCat;
$context['cats'] = Timber::get_terms('category');
$context['postType'] = 'post';
$context['authors'] = $authors;
$context['years'] = $years;
$context['months'] = $months;
$context['blog_url'] = $blog_url;

Timber::render('pages/category.twig', $context );
