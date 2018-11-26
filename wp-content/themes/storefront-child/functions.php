<?php

add_filter('show_admin_bar', '__return_false');


function remove_sf_actions() {
    remove_action( 'storefront_header', 'storefront_product_search', 40 );
    remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 );
    remove_action( 'storefront_header', 'storefront_primary_navigation', 50 );
    remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    remove_action( 'woocommerce_after_single_product_summary',    'storefront_single_product_pagination',     30 );
    remove_action( 'storefront_footer', 'storefront_handheld_footer_bar', 999);


    // remove_action( 'woocommerce_after_shop_loop', 'storefront_sorting_wrapper', 9 );
    // remove_action( 'woocommerce_after_shop_loop','storefront_sorting_wrapper_close', 31 );
    // remove_action( 'woocommerce_before_shop_loop','storefront_sorting_wrapper',9 );
    // remove_action( 'woocommerce_before_shop_loop','storefront_sorting_wrapper_close', 31 );
    

    remove_theme_support( 'wc-product-gallery-zoom' );
    remove_theme_support( 'wc-product-gallery-lightbox' );
    remove_theme_support( 'wc-product-gallery-slider' );
    
    
}
add_action( 'init', 'remove_sf_actions' );

remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
add_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 30 );


add_action( 'wp_enqueue_scripts', 'twentyf_enqueue_styles' );
function twentyf_enqueue_styles() {
  
    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );

    if ( !is_admin() ) { 
        wp_register_script('custom_script',
            get_bloginfo('stylesheet_directory') . '/js/function.js',
            array('jquery'),
            '1.1' );

        wp_register_script('payment_method',
            get_bloginfo('stylesheet_directory') . '/js/payment-method-hook.js',
            array('jquery'),
            '1.1', true );
     
        wp_enqueue_script('custom_script');
        wp_enqueue_script('payment_method');
        wp_localize_script( 'payment_method', 'wp_ajax',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
     }
}



  //Add options page
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}

function myplugin_tinymce_buttons( $buttons ) {
    //Add style selector to the beginning of the toolbar
    array_unshift( $buttons, 'styleselect' );

    return $buttons;
}
add_filter( 'mce_buttons_2', 'myplugin_tinymce_buttons' );


add_filter( 'tiny_mce_before_init', 'fb_mce_before_init' );

function fb_mce_before_init( $settings ) {

    $style_formats = array(
        array(
					'title' => 'Default Button',
					'selector' => 'a',
					'classes' => 'btn'
				),
				array(
					'title' => 'Arrow Button',
					'selector' => 'a',
					'classes' => 'btn-arrow'
				),
				array(
					'title' => 'Small Headline',
					'selector' => 'h1,h2,h3,h4,h5,h6',
					'classes' => 'small'
				)
    );

    $settings['style_formats'] = json_encode( $style_formats );

    return $settings;

}

//WORKSPACE ROLES
add_role(
	'dedicated_desk',
	__( 'Dedicated Desk' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'dedicated_desk_single',
	__( 'Dedicated Desk - Single Month' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'private_office',
	__( 'Private Office' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'starter',
	__( 'Starter' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'shared_space',
	__( 'Shared Space' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'shared_space_single',
	__( 'Shared Space - Single Month' ),
	array(
			'read'         => true,  // true allows this capability
	)
);

//SPONSOR ROLES
add_role(
	'founder',
	__( 'Founder' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'sustainer',
	__( 'Sustainer' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'supporter',
	__( 'Supporter' ),
	array(
			'read'         => true,  // true allows this capability
	)
);
add_role(
	'contributor',
	__( 'Contributor' ),
	array(
			'read'         => true,  // true allows this capability
	)
);

//Add ability to upload SVG
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
  }
  add_filter('upload_mimes', 'cc_mime_types');

function register_post_types() {

        register_post_type( 'event',
            array(
                'labels' => array(
                    'name' => __( 'Events' ),
                    'singular_name' => __( 'Event' ),
                    'add_new' => __( 'Add New Event' ),
                    'add_new_item' => __( 'Add New Event' ),
                    'edit' => __( 'Edit' ),
                    'edit_item' => __( 'Edit Event' ),
                    'new_item' => __( 'New Event' ),
                    'view' => __( 'View Event' ),
                    'view_item' => __( 'View Event' ),
                    'search_items' => __( 'Search Events' ),
                    'not_found' => __( 'No Events Found' ),
                    'not_found_in_trash' => __( 'No Events in Trash' )
                ),
                'public' => true,
                'rewrite' => true,
                'supports' => array(
                    'title', 'editor'
                )
            )
        );
    }

    function register_taxonomies() {
        $labels = array(
            'name' => _x( 'Event Host', 'taxonomy general name' ),
            'singular_name' => _x( 'Event Host', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Event Hosts' ),
            'all_items' => __( 'All Event Hosts' ),
            'parent_item' => __( 'Parent Event Hosts' ),
            'parent_item_colon' => __( 'Parent Event Host:' ),
            'edit_item' => __( 'Edit Event Host' ),
            'update_item' => __( 'Update Event Host' ),
            'add_new_item' => __( 'Add New Event Host' ),
            'new_item_name' => __( 'New Event Host' ),
            'menu_name' => __( 'Event Host' ),
        );
        register_taxonomy('eventhost',array('event'), array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'query_var' => true,
                'show_in_rest' => true,
                'rewrite' => array( 'slug' => 'event-host' ),
        ));
    }

    add_action( 'init', 'register_post_types' );
    add_action( 'init', 'register_taxonomies' );


    add_filter( 'woocommerce_subscriptions_is_recurring_fee', '__return_true' );
    //add_filter( 'woocommerce_cart_calculate_fees', 'add_transaction_fees', 10, 1 );
    
    add_action('wcs_user_removed_item', 'recalculate_transaction_fee', 10, 1);
    add_action('wcs_user_readded_item', 'recalculate_transaction_fee', 10, 1);

    function recalculate_transaction_fee($line_item) {
        $newOrderPrice = 0;
        $order_id = $line_item->get_order_id();
        $subscription = wcs_get_subscription( $order_id );
        $items = $subscription->get_items();

        foreach($items as $item) {
            $newOrderPrice += $item->get_total();
        }

        $newFee = ($newOrderPrice + .3)/(1-.029) - $newOrderPrice;

        $feesObj = $subscription->get_fees();

        $fee_reverse = array_reverse($feesObj);
        $fee = array_pop($fee_reverse);
        $fee->set_amount($newFee);
        $fee->set_total($newFee);

        $subscription->save();
    }
    
    function add_transaction_fees( $cart ) {
        $chosen_gateway = WC()->session->chosen_payment_method;
        // if ( $chosen_gateway == 'stripe' ) { //test with paypal method
            $fee = (WC()->cart->cart_contents_total + .3)/(1-.029) - WC()->cart->cart_contents_total;
            $cart->add_fee( 'Transaction Fee', $fee );
        // }
        
    }

    // function action_woocommerce_checkout_update_order_review($array, $int)
    // {
    //     $fee = (WC()->cart->cart_contents_total + .3)/(1-.029) - WC()->cart->cart_contents_total;
    //     WC()->cart->add_fee( 'Transaction Fee', $fee );
        
    // }
    // add_action('woocommerce_checkout_update_order_review', 'action_woocommerce_checkout_update_order_review', 10, 2);


    add_action( 'woocommerce_cart_calculate_fees', 'twentyf_calculate_cart_fees', 20, 1 );
 
    function twentyf_calculate_cart_fees( $cart ) {
    
        if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
            return;
        }
        
        $payment_method = WC()->session->chosen_payment_method;
            
        if ( "intuit_payments_echeck" == $payment_method ) {
            $fee = 0;
            $cart->add_fee( __('Transaction Fee', 'woocommerce'), $fee );
        } elseif ( "intuit_payments_credit_card" == $payment_method ) {
            $fee = (WC()->cart->cart_contents_total + .3)/(1-.028) - WC()->cart->cart_contents_total;
            $cart->add_fee( __('Transaction Fee', 'woocommerce'), $fee );
        }
    
    }


    add_action( 'wp_ajax_payment_method_changed', 'payment_method_changed' );
    add_action( 'wp_ajax_nopriv_payment_method_changed', 'payment_method_changed' );
    function payment_method_changed() {

        if ( isset($_POST['payment_method']) ){
            $payment_method = sanitize_key( $_POST['payment_method'] );

            WC()->session->set('payment_method_chosen', $payment_method );

            echo json_encode( $payment_method );
        }
        
        wp_die();
    }


    add_filter ( 'woocommerce_account_menu_items', 'twentyf_remove_my_account_links' );
    function twentyf_remove_my_account_links( $menu_links ){
     
        //unset( $menu_links['edit-address'] ); // Addresses
     
     
        //unset( $menu_links['dashboard'] ); // Dashboard
        //unset( $menu_links['payment-methods'] ); // Payment Methods
        //unset( $menu_links['orders'] ); // Orders
        unset( $menu_links['downloads'] ); // Downloads
        //unset( $menu_links['edit-account'] ); // Account details
        //unset( $menu_links['customer-logout'] ); // Logout
     
        return $menu_links;
     
    }


    function eg_remove_my_subscriptions_button( $actions, $subscription ) {
        foreach ( $actions as $action_key => $action ) {
            switch ( $action_key ) {
               // case 'change_payment_method':	// Hide "Change Payment Method" button?
    //			case 'change_address':		// Hide "Change Address" button?
    			case 'switch':			// Hide "Switch Subscription" button?
    //			case 'resubscribe':		// Hide "Resubscribe" button from an expired or cancelled subscription?
    //			case 'pay':			// Hide "Pay" button on subscriptions that are "on-hold" as they require payment?
    //			case 'reactivate':		// Hide "Reactive" button on subscriptions that are "on-hold"?
    			case 'cancel':			// Hide "Cancel" button on subscriptions that are "active" or "on-hold"?
                    unset( $actions[ $action_key ] );
                    break;
                default: 
                    error_log( '-- $action = ' . print_r( $action, true ) );
                    break;
            }
        }
        return $actions;
    }
    add_filter( 'wcs_view_subscription_actions', 'eg_remove_my_subscriptions_button', 100, 2 );


    // add_filter ( 'woocommerce_account_menu_items', 'custom_my_account_links' );
    // function custom_my_account_links( $menu_links ){
     
    //     // we will hook "anyuniquetext123" later
    //     $new = array( 'myavailablepruchases' => 'My Available Purchases' );
     
    //     // or in case you need 2 links
    //     // $new = array( 'link1' => 'Link 1', 'link2' => 'Link 2' );
     
    //     // array_slice() is good when you want to add an element between the other ones
    //     $menu_links = array_slice( $menu_links, 0, 1, true ) 
    //     + $new 
    //     + array_slice( $menu_links, 1, NULL, true );
     
     
    //     return $menu_links;
     
     
    // }
     
    // add_filter( 'woocommerce_get_endpoint_url', 'custom_my_account_hook_endpoint', 10, 4 );
    // function custom_my_account_hook_endpoint( $url, $endpoint, $value, $permalink ){
     
    //     if( $endpoint === 'myavailablepruchases' ) {
     
    //         // ok, here is the place for your custom URL, it could be external
    //         $url = site_url('/shop');
     
    //     }
    //     return $url;
     
    // }

    // add_action('woocommerce_before_account_navigation', 'my_account_content_before');
    // function my_account_content_before(){
    //     echo '<a href="/my-available-purchases" class="btn btn-my-account">My Available Purchases</a>';
    // }
    