<?php
/*
Template Name: Events
*/

get_header();

date_default_timezone_set('America/Detroit');

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

 ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

    <?php do_action( 'storefront_page_before' );

    if(get_field('page_header')) {
          while(has_sub_field('page_header')):
            if(get_row_layout() == 'image_page_header'): ?>
              <header class="header__image-page-header">
              <div class="container-cover-image">
                <?php $header_image = get_sub_field('header_image'); ?>
                <img src="<?php echo $header_image['url']; ?>" alt="<?php echo $header_image['alt']; ?>">
              </div>
              <div class="inner-container">
                <div class="row">
                  <div class="mobile-col-1-1">
                    <?php the_sub_field('header_text'); ?>
                  </div>
                </div>
              </div>
            </header>
          <?php endif;

          if(get_row_layout() == 'text_page_header'): ?>

            <header class="header__text-header">
              <div class="inner-container">
                <div class="row">
                  <div class="mobile-col-1-1 tablet-col-3-4__centered">
                    <?php the_sub_field('header_text'); ?>
                  </div>
                </div>
              </div>
            </header>

          <?php endif;

          endwhile;
        } ?>

    <section class="section__events">
      <div class="inner-container">
        <div class="column mobile-col-1-1 tablet-col-3-4__centered">
          <?php
            if($query->have_posts()) {
              while($query->have_posts()): $query->the_post(); ?>
                
              <div class="event-container">
                <div class="event-date">
                  <?php
                    $terms = get_the_terms(get_the_ID(), 'eventhost');
                    if($terms && $terms[0]->slug == '20fathoms') {
                      echo "<img src='".get_stylesheet_directory_uri()."/img/20F-black.svg' alt='20Fathoms Event'>";
                    }
                  ?>
                  <span class="date"><?php echo date('d', strtotime(get_field('event_start_date'))); ?></span>
                  <span class="month"><?php echo date('M', strtotime(get_field('event_start_date'))); ?></span>
                </div>
                <div class="event-info">
                  <h3><?php the_title(); ?></h3>
                  <?php 
                    $stripped = strip_tags(get_the_content());
                    $content = substr($stripped, 0, 150);
                  ?>
                  <p><?php echo $content; ?></p>
                  <a href="<?php echo get_field('event_link'); ?>" target="_blank" class="btn-arrow">Learn more</a>
                </div>
              </div>
                
            <?php
              endwhile;
              wp_reset_postdata();
            } 
          ?>
        </div>
      </div>
    </section>

    

    </div>
  </main>
  <?php
do_action( 'storefront_sidebar' );
get_footer();