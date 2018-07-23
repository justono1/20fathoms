<?php

class WooCommerce_Theme {
    public function __construct() {
        if ( class_exists( 'WooCommerce' ) ) {
            Timber\Integrations\WooCommerce\WooCommerce::init();
        }

        // Disable WooCommerce image functionality
        // Timber\Integrations\WooCommerce\WooCommerce::disable_woocommerce_images();

        add_action( 'after_setup_theme', [ $this, 'hooks' ] );
        add_action( 'after_setup_theme', [ $this, 'setup' ] );
    }

    /**
     * Customize WooCommerce
     *
     * @see plugins/woocommerce/includes/wc-template-hooks.php for a list of actions.
     *
     * Everything here is hooked to `after_setup_theme`, because child theme functionality runs before parent theme
     * functionality. By hooking it, we make sure it runs after all hooks in the parent theme were registered.
     */
    public function hooks() {
       // Add your hooks to customize WooCommerce here

       //Remove related products
       remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
       //Remove the product details tab navigation
       remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

       //remove archove page breadcrumb
       remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

       remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
       remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
    }

    /**
     * Setup.
     */
    public function setup() {
        add_theme_support( 'woocommerce' );
    }
}

new WooCommerce_Theme();