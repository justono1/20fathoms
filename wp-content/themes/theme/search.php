<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$templates = array( 'pages/search.twig', 'pages/archive.twig', 'pages/index.twig' );
$context = Timber::get_context();

global $paged;
global $wp_query;
if (!isset($paged) || !$paged){
    $paged = 1;
}

$startpost = 1;
$allposts = $wp_query->found_posts;

$context['startpost'] = 10*($paged - 1)+1;
$context['endpost'] = (10*$paged < $allposts ? 10*$paged : $allposts);
$context['allposts'] = $wp_query->found_posts;

$context['query'] = get_search_query();
$context['posts'] = Timber::get_posts();
$context['pagination'] = Timber::get_pagination();

$context['cats'] = Timber::get_terms('category');

Timber::render( $templates, $context );
