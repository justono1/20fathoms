<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;
//ACF Authors social links
$context['author_socials'] = get_field('social_links','user_'.$post->post_author);
//next is link to the blog page (Change ID)
$context['blog_url'] = '/blog';
$context['events_url'] = get_permalink(562);
$context['cats'] = Timber::get_terms('category');
Timber::render( array( 'pages/single-' . $post->ID . '.twig', 'pages/single-' . $post->post_type . '.twig', 'pages/single.twig' ), $context );
