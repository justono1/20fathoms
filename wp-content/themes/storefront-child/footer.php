<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

		</div><!-- .col-full -->
	</div><!-- #content -->

  <?php do_action( 'storefront_before_footer' ); ?>
  
  <footer class="footer">
	<div class="inner-container">
		<div class="row">
			<div class="mobile-col-1-1 desktop-col-1-3">
          <?php $footer_logo = get_field('footer_logo', 'option'); ?>
					<img src="<?php echo $footer_logo['url']; ?>" alt="<?php echo $footer_logo['alt']; ?>">
					<p><?php the_field('address', 'option'); ?></p>
			</div>
			<div class="mobile-col-1-1 desktop-col-2-3">
				<div class="row">
					<div class="mobile-col-1-1 tablet-col-1-2 footer-nav">
              <?php
              wp_nav_menu( array('menu' => 'primary', 'menu_class' => 'first-footer-nav') );
            
              $social_media = get_field('social_media', 'option');

              if($social_media): ?>

              <ul>
                <?php while(have_rows('social_media', 'option')): the_row(); ?>
                  <li>
                    <a href="<?php echo get_sub_field('social_link'); ?>" target="_blank">
                      <?php $social_icon = get_sub_field('social_icon'); ?>
                      <img src="<?php echo $social_icon['url']; ?>" alt="<?php echo $social_icon['alt']; ?>"><span class="visually-hidden"><?php the_sub_field('social_media_name'); ?></span>
                    </a>
                  </li>
               <?php endwhile; ?>
							</ul>

            <?php
              endif;
            ?>
					</div>
					<div class="mobile-col-1-1 tablet-col-1-2">
						<?php the_field('footer_cta', 'option'); ?>
					</div>
				</div>
			</div>
    </div>

    <?php
			/**
			 * Functions hooked in to storefront_footer action
			 *
			 * @hooked storefront_footer_widgets - 10
			 * @hooked storefront_credit         - 20
			 */
      remove_action('storefront_footer', 'storefront_credit', 20);
      do_action( 'storefront_footer' ); ?>
      
	</div>
</footer>
<div class="sub-footer">
	<p>&copy; Copyright <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?></p>
</div>

	<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
