<?php
/*
Template Name: Homepage
*/

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['header_class'] = $post->post_name;

Timber::render( array( 'page-homepage.twig', 'page.twig' ), $context );
