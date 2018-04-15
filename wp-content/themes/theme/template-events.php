<?php
/*
Template Name: Events
*/

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

date_default_timezone_set(get_option('timezone_string'));

$args = array(
  'post_type' => 'event',
  'posts_per_page' => -1,
  'order' => 'ASC',
  'orderby' => 'meta_value',
  'meta_type' => 'DATETIME',
  'meta_query' => array(
    array(
      'key' => 'event_start_date',
      'value' => date('Y-m-d H:i:s'),
      'compare' => '>='
    )
  )
);

$query = new WP_Query($args);

$context['events'] = Timber::get_posts($query);

Timber::render( array( 'pages/page-events.twig' ), $context );