<?php

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
// if ( class_exists( 'WooCommerce' ) ) {
// 	require get_template_directory() . '/woocommerce.php';
// }


function activate_default_plugins() {
	activate_plugin("advanced-custom-fields-pro/acf.php");
	activate_plugin("timber-library/timber.php");
}
add_action( 'admin_init', 'activate_default_plugins' );

add_filter('show_admin_bar', '__return_false');

function wpb_adding_scripts() {
	wp_register_script('scripts_js', get_stylesheet_directory_uri().'/js/scripts.js', array('jquery-core'), '1.0', true);
	wp_register_script('font_awesome', 'https://use.fontawesome.com/6996d5cac8.js', array(), '1.0', true);

	wp_enqueue_script('jquery-core');
  wp_enqueue_script('scripts_js');

    //Register a script per page
	// if (is_page_template('template-page-solutions_wizard.php')){
	// 	wp_register_script('solution_wizard', get_stylesheet_directory_uri().'/js/solution-wizard.js', array('jquery'), '1.0', true);
	// 	wp_enqueue_script('solution_wizard');
	// }
}


add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' );

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}


//Register Nav Menus
register_nav_menus( array(
	'main_nav' => 'Main Navigation',
) );


function timber_set_product( $post ) {
	global $product;
	
	if ( is_woocommerce() ) {
			$product = wc_get_product( $post->ID );
	}
}

Timber::$dirname = array('templates', 'views');
TimberLoader::CACHE_NONE;


class INSTANCE extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		parent::__construct();
	}

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

	function add_to_context( $context ) {
		$context['mainMenu'] = new TimberMenu('main_nav');
		$context['site'] = $this;
		$context['ajax_url'] = admin_url('admin-ajax.php');
		$context['site_options'] = get_fields('options');
		$context['is_home'] = is_front_page();
		return $context;
	}

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

	function add_to_twig( $twig ) {
		/* this is where you can add your own fuctions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter( 'jsdata', new Twig_Filter_Function( 'jsdata' ) );
		$twig->addFilter( 'stripHtmlTags', new Twig_Filter_Function( 'stripHtmlTags' ) );
		$twig->addFilter( 'filter_ptags_on_images', new Twig_Filter_Function( 'filter_ptags_on_images' ) );
		$twig->addFilter( 'get_thumbnail_link', new Twig_Filter_Function( 'get_thumbnail_link' ) );
		$twig->addFilter( 'get_grid_class', new Twig_Filter_Function( 'get_grid_class' ) );
		$twig->addFilter( 'get_grid_remainder', new Twig_Filter_Function( 'get_grid_remainder' ) );

		$twig->addFunction(new Timber\Twig_Function(
			'addClass',
			array($this, 'addClass')
		));

		return $twig;
	}

}

new instance();


//Various useful function
function jsdata( $text ) {
	return str_replace(" ", "-", strtolower($text));
}

function stripHtmlTags($text) {
	return strip_tags($text);
}

function filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

function get_thumbnail_link($ID){
	 return   wp_get_attachment_image_url(get_post_thumbnail_id($ID));
}

//Add custom image size
// add_filter( 'image_size_names_choose', 'my_custom_image_sizes' );

// function my_custom_image_sizes( $sizes ) {
//     return array_merge( $sizes, array(
//         'post-feed-image' => __( 'Post Feed Image' ),
//     ) );
// }
//
// add_image_size('post-feed-image', 371, 216, true);


//Search through only blog posts
// function SearchFilter($query) {
// if ($query->is_search) {
// $query->set('post_type', 'post');
// }
// return $query;
// }
//
// add_filter('pre_get_posts','SearchFilter');


//Add ability to upload SVG
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


// Adding google map API key
/*
function my_acf_init() {
    acf_update_setting('google_api_key', 'api_key');
}
add_action('acf/init', 'my_acf_init');
*/


//Add options page
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();
}


//Render template as shortcode
function process_map( $atts ){
	$context = Timber::get_context();
	return Timber::compile( 'templates/sections/process_map.twig', $context);
}
add_shortcode( 'process-map', 'process_map' );


function year_shortcode() {
  $year = date('Y');
  return $year;
}
add_shortcode('year', 'year_shortcode');



//Required for Tiled Grid Block
function get_grid_class($column_amount){
	if ($column_amount <= 2) {
		$class = 'tablet-col-1-2__flex';
	} else if ($column_amount == 3) {
		$class = 'tablet-col-1-3__flex';
	} else if ($column_amount == 5) {
		$class = 'tablet-col-1-3__flex';
	} else if (($column_amount % 4) == 0) {
		$class = 'tablet-col-1-4__flex';
	} else if ($column_amount >= 10) {
		$class = 'tablet-col-1-4__flex';
	} else if (($column_amount % 3) == 0) {
		$class = 'tablet-col-1-3__flex';
	} else {
		$class = 'tablet-col-1-4__flex';
	}
	return $class;
}

function get_grid_remainder($column_amount){
	if ($column_amount <= 2) {
		$remainder = 0;
	} else if ($column_amount == 3) {
		$remainder = 0;
	} else if ($column_amount == 5) {
		$remainder = 1;
	} else if (($column_amount % 4) == 0) {
		$remainder = 4 - ($column_amount % 4);
	} else if ($column_amount >= 10) {
		$remainder = 4 - ($column_amount % 4);
	} else if (($column_amount % 3) == 0) {
		$remainder = 0;
	} else {
		$remainder = 4 - ($column_amount % 4);
	}
	return $remainder;
}



//Add button dropdown option to tinymce

function myplugin_tinymce_buttons( $buttons ) {
      //Add style selector to the beginning of the toolbar
      array_unshift( $buttons, 'styleselect' );

      return $buttons;
 }
add_filter( 'mce_buttons_2', 'myplugin_tinymce_buttons' );


/**
 * Add styles/classes to the "Styles" drop-down
 */
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
