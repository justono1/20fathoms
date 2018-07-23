<?php
/*
Template Name: Page Builder
*/

function addClass($classArr) {
  $classes = '';

  foreach ($classArr as $value) {
    if(!$value == '') {
      $classes .= $value. " ";
    }
  }

  $classes = rtrim($classes);

  return 'class="'.$classes.'"';
}

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post();

        do_action( 'storefront_page_before' );
        
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
        }

        if(get_field('page_layout')):
          
          while(has_sub_field('page_layout')):

            /**
             * BLOCK: Text Plus Image
             */
            if(get_row_layout() == 'text_plus_image'): 
              
              $image_alignment = get_sub_field('image_alignment');            
            ?>

            <section class="section__text_plus_image">
              <div class="inner-container">
                <div class="row__flex row__flex-align-items-center<?php echo ($image_alignment == 'right') ? '' : ' row__flex-reverse'; ?>">
                  <div class="column mobile-col-1-1 tablet-landscape-col-3-8">
                    <?php the_sub_field('content'); ?>
                  </div>
                  <div class="column mobile-col-1-1 tablet-landscape-col-5-8 flex-image__container">
                    <?php
                      $count = 0;
                      $images = get_sub_field('image_group');

                      foreach($images as $image) {
                        ?>
                        <div class="flex-image__image-<?php echo $count; ?>">
                          <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
                        </div>
                        <?php
                        $count++;
                      }
                    ?>
                  </div>
                </div>
              </div>
            </section>

            <?php endif;
            /**
             * BLOCK: Call To Action
             */
            if(get_row_layout() == 'call_to_action'): ?>

              <section class="section__call-to-action">
                <div class="inner-container">
                  <div class="row">
                    <div class="desktop-col-7-8__centered">
                      <div class="row__flex row__flex-align-items-center">
                        <div class="tablet-col-2-12">
                          <?php $cta_icon = get_sub_field('cta_icon'); ?>
                          <img src="<?php echo $cta_icon['url']; ?>" alt="<?php echo $cta_icon['alt']; ?>" <?php echo ($cta_icon['mime_type'] == 'image/svg+xml') ? 'style="width: 100%;"' : ''; ?>>
                        </div>
                        <div class="tablet-col-1-2">
                          <?php the_sub_field('cta_text'); ?>
                        </div>
                        <div class="tablet-col-1-3">

                          <?php
                            while(have_rows('cta_buttons')): the_row();
                              $destination = get_sub_field('cta_button_destination');
                              $target = 'same';
                              if($destination == 'internal') {
                                $link = get_sub_field('button_internal_link');
                              } else {
                                $target = get_sub_field('link_target');
                                $link = get_sub_field('button_external_link');
                              } ?>
                              <a href="<?php echo $link; ?>" class="btn btn-primary" <?php echo ($target == 'new') ? 'target="_blank"' : ''; ?>><?php the_sub_field('cta_button_title'); ?></a>
                            <?php   
                            endwhile;
                          
                          ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

            <?php endif;
            /**
             * BLOCK: Cards
             */
            if(get_row_layout() == 'cards'):

              $cardCount = count(get_sub_field('cards'));
              $columnClass = 'mobile-col-1-1';
              if($cardCount > 1) {
                $columnClass .= ' tablet-col-1-2';
              }
              if($cardCount > 2) {
                $columnClass .= 'desktop-col-1-3';
              }

              ?>

              <section class="section__cards">
                <div class="inner-container">
                  <h2><?php the_sub_field('section_title'); ?></h2>
                  <div class="row__flex">
                    <?php
                    while(have_rows('cards')): the_row(); ?>
                    <div class="card <?php echo $columnClass; ?>">
                      <div class="card__image">
                        <?php $image = get_sub_field('card_image'); ?>
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
                      </div>
                      <div class="card__content">
                        <?php the_sub_field('card_contet');

                        if(get_sub_field('has_link')) {
                          if(get_sub_field('card_link_destination') == 'internal') { ?>
                            <a href="<?php the_sub_field('internal_link'); ?>" class="card-link"><?php the_sub_field('link_text'); ?></a>
                          <?php } else { ?>
                            <a href="<?php the_sub_field('external_link'); ?>" target="_blank" class="card-link"><?php the_sub_field('link_text'); ?></a>
                        <?php
                          }
                        } ?>
                      </div>
                    </div>
                    <?php endwhile; ?>
                  </div>
                </div>
              </section>

            <?php endif; 
            /**
             * BLOCK: columns
             */
            if(get_row_layout() == 'columns'): ?>

              <section class="section__columns">
                <div class="inner-container">
                      
                  <?php if(get_sub_field('section_title')): ?>
                    <h2><?php the_sub_field('section_title'); ?></h2>
                  <?php endif; ?>
                  
                  <?php
                    $column = get_sub_field('column_layout');
                    $card = get_sub_field('column_cards');
                    $flexAlign = ($column == 'flex') ? get_sub_field('flex_align_columns') : 'false';

                    $rowClass = array(
                      $column == 'flex' ? 'row__flex': 'row',
                      $flexAlign && $flexAlign == 'center' ? 'row__flex-align-items-center' : '',
                      $card ? 'column-cards' : '',
                    );

                    $columnClass = array(
                      'column',
                      $column == 'one' ? 'mobile-col-1-1' : '',
                      $column == 'two' ? 'mobile-col-1-1 tablet-col-1-2' : '',
                      $column == 'three' ? 'mobile-col-1-1 tablet-col-1-3' : '',
                      $column == 'four' ? 'mobile-col-1-1 tablet-col-1-2 desktop-col-1-4' : '',
                      $column == 'five' ? 'mobile-col-1-1 tablet-col-1-2 desktop-col-1-5' : '',
                      $column == 'flex' ? 'column-item' : ''
                    );
                  ?>
                  <div <?php echo addClass($rowClass); ?>>
                    <?php
                      while(have_rows('column_content')): the_row(); ?>
                        <div <?php echo addClass($columnClass); ?>>
                          <?php 
                          $image = get_sub_field('column_image'); 
                          if($image) { ?>
                            <div class="column-item__image">
                              <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
                            </div>
                          <?php }

                          if(get_sub_field('column_text')) { ?>
                            <div class="column-item__content">
                              <?php the_sub_field('column_text'); ?>
                            </div>
                          <?php } ?>
                        </div>
                      <?php endwhile;
                    ?>
                  </div>
                </div>
              </section>
            
            <?php endif;
            /**
             * BLOCK: form_block
             */
            if(get_row_layout() == 'form_block'): ?>

              <section class="section__form-block">
                <div class="inner-container">
                  <div class="row">
                    <div class="mobile-col-1-1 tablet-col-5-6__centered tablet-landscape-col-1-2__centered">
                      <div class="wysiwyg">
                        <?php the_sub_field('form_content'); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

            <?php endif;
            /**
             * BLOCK: full column text
             */
            if(get_row_layout() == 'full_column_text'): ?>

              <section>
                <div class="inner-container">
                  <div class="row">
                    <div class="mobile-col-6-8__centered">
                      <div class="wysiwyg">
                        <?php the_sub_field('content'); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </section>

            <?php endif; 
            /**
             * BLOCK: logo section
             */
            if(get_row_layout() == 'logo_section'): ?>

              <section class="section__logos">
                <div class="inner-container text-center">

                  <?php if(get_sub_field('logo_section_title')): ?>
                    <h2><?php the_sub_field('logo_section_title'); ?></h2>
                  <?php endif; ?>

                  <div class="row__flex row__flex-align-center">
                    <?php
                      $logos = get_sub_field('logos');
                      foreach($logos as $logo) { ?>
                        <div class="tablet-col-1-3 mobile-col-1-2 desktop-col-1-4">
                          <img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>">
                        </div>
                    <?php } ?>
                  </div>
                </div>
              </section>
            
            <?php endif; 
            /**
             * BLOCK: people
             */
            if(get_row_layout() == 'people'): ?>

              <section class="section__people-grid">
                <div class="inner-container">
                  <?php
                  while(have_rows('people')): the_row();
                    $peopleInRow = count(get_sub_field('people_info')); ?>
                  <div class="row <?php echo ($peopleInRow < 2) ? ' person_single': ''; ?>">

                    <?php if(get_sub_field('section_title')): ?>
                    <h2><?php the_sub_field('section_title'); ?></h2>
                    <?php endif; ?>
                    
                    <div class="column mobile-col-1-1">
                      <?php while(have_rows('people_info')): the_row(); ?>
                        <div class="person mobile-col-1-1__flex tablet-col-1-2__flex tablet-landscape-col-1-3__flex">
                          <div class="person__image">
                            <?php 
                            $image = get_sub_field('person_image');
                            if($image): ?>
                              <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
                            <?php else: ?>
                              <span></span>
                            <?php endif; ?>
                          </div>
                          <div class="person_info">
                            <h3><?php the_sub_field('person_name'); ?></h3>
                            <?php if(get_sub_field('person_bio')): ?>
                              <p><?php the_sub_field('person_bio'); ?></p>
                            <?php endif; ?>
                          </div>
                        </div>
                      <?php endwhile; ?>
                    </div>
                  </div>
                  <?php endwhile; ?>
                </div>
              </section>
            
            <?php endif; 

          endwhile;

        endif;

				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				do_action( 'storefront_page_after' );

			endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
