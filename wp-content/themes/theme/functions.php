<?php

function activate_default_plugins() {
	activate_plugin("advanced-custom-fields-pro/acf.php");
	activate_plugin("timber-library/timber.php");
}
add_action( 'admin_init', 'activate_default_plugins' );

add_filter('show_admin_bar', '__return_false');

function wpb_adding_scripts() {
	wp_register_script('function_js', get_stylesheet_directory_uri().'/js/function.js', array('jquery-core'), '1.0', true);
	wp_register_script('google_maps_api', 'https://maps.googleapis.com/maps/api/js?key=APIKEY', array(), null, true);
	wp_register_script('google_maps', get_stylesheet_directory_uri().'/js/google-maps.js', array('prts_google_maps_api'), '1.0', true);
	wp_register_script('font_awesome', 'https://use.fontawesome.com/6996d5cac8.js', array(), '1.0', true);
    wp_register_script('vimeo', 'https://player.vimeo.com/api/player.js', array(), '1.0', true);

    wp_enqueue_script('function_js');
	wp_enqueue_script('google_maps_api');
	wp_enqueue_script('google_maps');
	wp_enqueue_script('jquery-core');
	wp_enqueue_script('font_awesome');
    wp_enqueue_script('vimeo');

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

		// register_post_type( 'form_backgrounds',
		// 	array(
		// 		'labels' => array(
		// 			'name' => __( 'Form Backgrounds' ),
		// 			'singular_name' => __( 'Form Background' ),
		// 			'add_new' => __( 'Add New Form Background' ),
		// 			'add_new_item' => __( 'Add New Form Background' ),
		// 			'edit' => __( 'Edit' ),
		// 			'edit_item' => __( 'Edit Form Background' ),
		// 			'new_item' => __( 'New Form Background' ),
		// 			'view' => __( 'View Form Background' ),
		// 			'view_item' => __( 'View Form Background' ),
		// 			'search_items' => __( 'Search Form Backgrounds' ),
		// 			'not_found' => __( 'No Form Backgrounds Found' ),
		// 			'not_found_in_trash' => __( 'No Form Backgrounds in Trash' )
		// 		),
		// 		'public' => true,
		// 		'rewrite' => true,
		// 		'supports' => array(
		// 			'title', 'thumbnail'
		// 		)
		// 	)
		// );
	}

    function register_taxonomies() {
		// $labels = array(
		// 	'name' => _x( 'Geography', 'taxonomy general name' ),
		//     'singular_name' => _x( 'Geography', 'taxonomy singular name' ),
		//     'search_items' =>  __( 'Search Geographys' ),
		//     'all_items' => __( 'All Geographys' ),
		//     'parent_item' => __( 'Parent Geographys' ),
		//     'parent_item_colon' => __( 'Parent Geography:' ),
		//     'edit_item' => __( 'Edit Geography' ),
		//     'update_item' => __( 'Update Geography' ),
		//     'add_new_item' => __( 'Add New Geography' ),
		//     'new_item_name' => __( 'New Geography' ),
		//     'menu_name' => __( 'Geography' ),
  // 		);
        //
		// register_taxonomy('geography',array('kcylocation'), array(
		//     'hierarchical' => true,
		//     'labels' => $labels,
		//     'show_ui' => true,
		//     'query_var' => true,
		// 		'show_in_rest' => true,
		//     'rewrite' => array( 'slug' => 'geography' ),
		// ));
    }

	function add_to_context( $context ) {
		$context['mainMenu'] = new TimberMenu('main_nav');
		$context['site'] = $this;
		$context['ajax_url'] = admin_url('admin-ajax.php');
		$context['site_options'] = get_fields('options');
		return $context;
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
                    'title' => 'Button',
                    'selector' => 'a',
                    'classes' => 'btn'
            )
    );

    $settings['style_formats'] = json_encode( $style_formats );

    return $settings;

}
