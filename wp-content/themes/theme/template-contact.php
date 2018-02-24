<?php
/*
Template Name: Contact
*/

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['header_class'] = $post->post_name;

Timber::render( array( 'pages/page.twig' ), $context );
