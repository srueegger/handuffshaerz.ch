<?php
/***************************************
 *	 CREATE GLOBAL VARIABLES
 ***************************************/
define( 'HOME_URI', home_url() );
define( 'THEME_URI', get_template_directory_uri() );
define( 'THEME_IMAGES', THEME_URI . '/dist-assets/images' );
define( 'DEV_CSS', THEME_URI . '/dev-assets/css' );
define( 'DEV_JS', THEME_URI . '/dev-assets/js' );
define( 'DIST_CSS', THEME_URI . '/dist-assets/css' );
define( 'DIST_JS', THEME_URI . '/dist-assets/js' );


/***************************************
 * Include helpers
 ***************************************/
require_once 'inc/gravityforms.php';
require_once 'inc/disable-gutenberg.php';
if(!WP_DEBUG):
	require_once 'inc/acf.php';
endif;

/***************************************
 * 		Theme Support and Options
 ***************************************/
add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
add_theme_support( 'title-tag' );
add_theme_support( 'menus' );

/***************************************
 * Custom Image Size
 ***************************************/
add_image_size( 'fullwidth-xl', 1920, 1080, true );
add_image_size( 'fullwidth-lg', 992, 661, true );
add_image_size( 'fullwidth-md', 768, 512, true );
add_image_size( 'fullwidth-sm', 576, 384, true );
add_image_size( 'fullwidth-xs', 400, 267, true );
add_image_size( 'partnerlogo-xl', 508, 9999, false );
add_image_size( 'partnerlogo-lg', 420, 9999, false );
add_image_size( 'partnerlogo-md', 660, 9999, false );
add_image_size( 'partnerlogo-sm', 352, 9999, false );
add_image_size( 'partnerlogo-xs', 258, 9999, false );
add_image_size( 'teamfoto-xl', 350, 441, true );
add_image_size( 'teamfoto-lg', 320, 416, true );
add_image_size( 'teamfoto-md', 510, 643, true );
add_image_size( 'teamfoto-sm', 545, 687, true );
add_image_size( 'teamfoto-xs', 290, 365, true );

/***************************************
 * Add Wordpress Menus
 ***************************************/
function register_huh_menu() {
	register_nav_menu( 'main-menu', 'Hauptmenü' );
	register_nav_menu( 'footer-menu', 'Footermenü' );
}
add_action( 'after_setup_theme', 'register_huh_menu' );

/***************************************
 * 		Enqueue scripts and styles.
 ***************************************/
function huh_startup_scripts() {
	//Google Fonts
	wp_enqueue_style( 'huh-google-font', 'https://fonts.googleapis.com/css?family=Montserrat+Alternates:400,700|Montserrat:400,700' );
	//Google Maps
	wp_enqueue_script( 'huh-google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDtvo159H5x0G9qus_ZJXIvaPy9vIEz7bM&language=de-CH&region=CH', null, null, true );
	if (WP_DEBUG) {
		wp_enqueue_style( 'huh-style', DEV_CSS . '/theme.css', array('huh-google-font'), '1.2' );
		wp_register_script( 'huh-script', DEV_JS ."/theme.js", array('jquery', 'huh-google-maps'), '1.2', true );
	} else {
		wp_enqueue_style( 'huh-style', DIST_CSS . '/theme.min.css', array('huh-google-font'), '1.2' );
		wp_register_script( 'huh-script', DIST_JS ."/theme.min.js", array('jquery', 'huh-google-maps'), '1.2', true );
	}
	$global_vars = array(
		'ajaxurl' => admin_url('admin-ajax.php')
	);
	wp_localize_script( 'huh-script', 'global_vars', $global_vars );
	wp_enqueue_script( 'huh-script' );
}
add_action( "wp_enqueue_scripts", "huh_startup_scripts" );

/***************************************
 * 		huh ACF Init
 ***************************************/
function huh_acf_init() {
	 $args = array(
		'page_title' => 'Einstellungen für die Webseite',
		'menu_title' => 'Theme Einstellungen',
		'menu_slug' => 'huh-theme-settings',
		'parent_slug' => 'options-general.php',
	);
	acf_add_options_sub_page($args);
	//Google Maps initialisieren
	acf_update_setting('google_api_key', 'AIzaSyDtvo159H5x0G9qus_ZJXIvaPy9vIEz7bM');
}
add_action( 'acf/init', 'huh_acf_init' );

/***************************************
 * Remove Menus from Backend
 ***************************************/
function huh_remove_menus() {
	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'huh_remove_menus' );

/***************************************
 * 	Add Admin CSS und JS File
 ***************************************/
function huh_admin_style_scripts() {
	wp_enqueue_style( 'huh-admin-css', DIST_CSS.'/admin/huh-admin-css.css', null, '1' );
}
add_action('admin_enqueue_scripts', 'huh_admin_style_scripts');

/***************************************
 * 	Print Menu item
 ***************************************/
function huh_print_menu_items($menuitems, $class = '') {
	if($class != ''):
		$printclass = ' class="'.$class.'"';
	else:
		$printclass = '';
	endif;
	if(!empty($menuitems)):
		echo '<ul'.$printclass.' role="tablist">';
		foreach($menuitems as $menuitem):
			echo '<li class="nav-item"><a class="nav-link" href="'.$menuitem->url.'">'.$menuitem->title.'</a></li>';
		endforeach;
		echo '</ul>';
	endif;
}

/***************************************
 * 	ACF Field mit Formularauswahlmöglichkeiten füllen
 ***************************************/
function huh_load_forms_to_acf_selectmenu( $field ) {
	$field['choices'] = array();
	$forms = GFAPI::get_forms();
	if(!empty($forms)):
		foreach($forms as $form):
			$field['choices'][$form['id']] = $form['title'];
		endforeach;
	endif;
	return $field;
}
add_filter('acf/load_field/name=front_s6_form_id', 'huh_load_forms_to_acf_selectmenu');