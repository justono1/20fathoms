<?php

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
  
      $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
  
      wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
      wp_enqueue_style( 'child-style',
          get_stylesheet_directory_uri() . '/style.css',
          array( $parent_style ),
          wp_get_theme()->get('Version')
      );
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

add_role(
	'dedicated_desk',
	__( 'Dedicated Desk' ),
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