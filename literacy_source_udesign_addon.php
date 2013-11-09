<?php
/*
Plugin Name: Literacy Source U-Design Addon
Description: Faciliates users without any code experience to create simple and elegant front-end elements using U-design's Theme. Note: Does not function without U-design theme, and suggested not to update U-design without verifying this plugin is supported. (please see readme.txt for all dependant U-design functions. Custom Homepage Sliders only work with slider 5: Cycle 2 (image with text).
 
Author: Steven Stevenson 
Author URI" http://www.stevenandleah.com
Version: 1.0
*/
/* init registering */
add_action( 'init', 'ss_register_ls_addon' );
//add_action('admin_menu','ss_ls_addon_settings');

/* include options page */
function ss_ls_addon_settings() {
	add_options_page('Literacy Source U-Design Addon Settings', 'U-Design Addon Settings', 'manage_options', 'udesign_options', 'ls_addon_options_page');
}

function ls_addon_options_page() {
		include('settings.php');
}

/* register slider type */
function ss_register_ls_addon() {
	$slider_labels = array(
		'name' => __( 'Sliders'),
		'singular_name' => __( 'Slide'),
		'add_new' => __( 'Add New'),
		'add_new_item' => __( 'Add New Hompage Slide'),
		'edit' => __( 'Edit'),
		'edit_item' => __( 'Edit Slide'),
		'new_item' => __( 'New Slide'),
		'view' => __( 'View Slide'),
		'view_item' => __( 'View Slide'),
		'search_items' => __( 'Search Slides'),
		'not_found' => __( 'No Slides Found'),
		'not_found_in_trash' => __( 'No Slides found in Trash'),
	);
	$slider_args = array(
		'labels' => $slider_labels,
		'capability_type' => 'post',
		'public' => true,
		'show_ui' => true,
		'can_export' => true,
		'has_archive' => true,
		'query_var' => true,
		'menu_icon' => plugins_url('attendance_list/images/menu-image-single.png'),  // Icon Path
		'rewrite' => array( 'slug' => 'attendance_list', 'with_front' => false ),
		'supports' => array( 'title','thumbnail')
	);

	register_post_type( 'homepage_sliders', $slider_args );
	global $current_user;
	global $user_level;
	get_currentuserinfo();
	if ( $user_level < 9 ) {
		add_filter( 'custom_menu_order', 'toggle_custom_menu_order' );
		add_filter( 'menu_order', 'remove_those_menu_items' );
	}
}

function toggle_custom_menu_order(){
	return true;
}
function remove_those_menu_items( $menu_order ){
	global $menu;

	foreach ( $menu as $mkey => $m ) {
		$key = array_search( 'edit.php?post_type=homepage_sliders', $m );
		if ( $key )
			unset( $menu[$mkey] );
	}
	return $menu_order;
}
	

// Styling for the custom post type icon
function ls_homepage_sliders_icons() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-homepage_sliders.wp-menu-image {
            background: url('<?php echo plugins_url('literacy_source_udesign_addon/images/menu-image.png'); ?>') no-repeat 6px -16px !important;
        }
		#menu-posts-homepage_sliders:hover .wp-menu-image, #menu-posts-portfolio.wp-has-current-submenu .wp-menu-image {
            background-position:6px -16px !important;
        }
		#icon-edit.icon32-posts-homepage_sliders {background: url('<?php echo plugins_url('literacy_source_udesign_addon/images/menu-image.png'); ?>') no-repeat 6px 9px; }
    </style>
<?php }
add_action( 'admin_head', 'ls_homepage_sliders_icons' );

 
/*********** START HOMEPAGE SLIDER METABOX *************/ 

// hide certain meta boxes on the 'homepage_sliders' custom post type
function hide_meta_boxes_sliders() {
 
	remove_meta_box('postexcerpt', 'homepage_sliders', 'normal');
	remove_meta_box('trackbacksdiv', 'homepage_sliders', 'normal');
	remove_meta_box('postcustom', 'homepage_sliders', 'normal');
	remove_meta_box('slugdiv', 'homepage_sliders', 'normal');
	remove_meta_box('commentstatusdiv', 'homepage_sliders', 'normal');
	remove_meta_box('commentsdiv', 'homepage_sliders', 'normal');
	remove_meta_box('revisionsdiv', 'homepage_sliders', 'normal');
 
}
add_filter('add_meta_boxes', 'hide_meta_boxes_sliders');

//function for primary metabox
function ls_slider_function( $post ) {
	include('slider_metabox.php');
}


//create and attach metabox to sliders
function ls_slider_mb_create() {
	add_meta_box('ls_slider_metabox', 'Literacy Source Slider Information', 'ls_slider_function', 'homepage_sliders', 'normal', 'high' );
}
add_action('add_meta_boxes', 'ls_slider_mb_create' );

//attach media upload to metabox
function ls_slider_admin_scripts() {
	wp_enqueue_script('ls-image-upload', 
			plugins_url('literacy_source_udesign_addon/js/settings-image.js'), 
			array('jquery','media-upload','thickbox' ) 
	);
}
add_action('admin_print_scripts-post.php', 'ls_slider_admin_scripts');
add_action('admin_print_scripts-post-new.php', 'ls_slider_admin_scripts');

//check permissions for posts
function checkPermissions() {  
  	if ( 'homepage_sliders' == $_POST['post_type'] ) {
    	if ( current_user_can( 'edit_page', $post_id ) ) {
        	return true;
		}
 	}
	return false;
}

/* saving post/mtea information */
function ls_slider_save_post( $post_id ) {
	if (!checkPermissions()) {
		return;
	}
	add_post_meta($post_id, '_ls_slider_text', strip_tags($_POST['ls_slider_text']) , true ) or update_post_meta( $post_id, '_ls_slider_text', strip_tags($_POST['ls_slider_text']) );
add_post_meta($post_id, '_ls_slider_link_type', strip_tags($_POST['ls_slider_link_type']) , true ) or update_post_meta( $post_id, '_ls_slider_link_type', strip_tags($_POST['ls_slider_link_type']) );
add_post_meta($post_id, '_ls_slider_link', strip_tags($_POST['ls_slider_link']) , true ) or update_post_meta( $post_id, '_ls_slider_link', strip_tags($_POST['ls_slider_link']) );
add_post_meta($post_id, '_ls_slider_page_id', strip_tags($_POST['ls_slider_page_id']) , true ) or update_post_meta( $post_id, '_ls_slider_page_id', strip_tags($_POST['ls_slider_page_id']) );
add_post_meta($post_id, '_ls_slider_image', strip_tags($_POST['ls_slider_image']) , true ) or update_post_meta( $post_id, '_ls_slider_image', strip_tags($_POST['ls_slider_image']) );


	$the_title = get_the_title($post_id);
	$the_text = strip_tags($_POST['ls_slider_text']);
	$the_text = "<h2>".$the_title."</h2>
<p>".$the_text."</p>
";
	$the_img = strip_tags($_POST['ls_slider_image']);
	$the_link = '';
	$target = 'self';
	if ( strip_tags($_POST['ls_slider_link_type']) == 'link_page') {
		$the_link = get_page_link(intval( strip_tags($_POST['ls_slider_page_id']) ));
	} else {
		$the_link = strip_tags($_POST['ls_slider_link']);
		$target = 'blank';
	}
	//static (open for advanced options) variables 
	$transition_type = 'fade';
	$alt = $the_title;
	$button_text = 'Read More';
	$button_style = 'dark';
	$speed = 1500;
	$timeout = 5000;
	$text_color = '333333';
	$text_size = '1.2';
	$text_line_height = '1.7';
	
	global $udesign_options;
	$udesign_options = get_option('udesign_options');
	$post_id_array = explode( ',', $udesign_options['c2_slides_order_str'] );
	$found = false;
	foreach ( $post_id_array as $id) {
		if (intval($id) == $post_id ) {
			$found = true;
			break;
		}
	}
	if (!$found) {
		$udesign_options['c2_slides_order_str'] = $udesign_options['c2_slides_order_str'].','.$post_id;
	}
	$udesign_options['c2_slide_img_url_'.$post_id] = $the_img;
	$udesign_options['c2_transition_type_'.$post_id] = $transition_type;
	$udesign_options['c2_slide_link_url_'.$post_id] = $the_link;
	$udesign_options['c2_slide_link_target_'.$post_id] = $target;
	$udesign_options['c2_slide_image_alt_tag_'.$post_id] = $alt;
	$udesign_options['c2_slide_default_info_txt_'.$post_id] = $the_text;
	$udesign_options['c2_slide_button_txt_'.$post_id] = $button_text;
	$udesign_options['c2_slide_button_style_'.$post_id] = $button_style;

	if ( !isset($udesign_options['c2_speed']) ) {
		$udesign_options['c2_speed'.$slide_id] = $speed;
	}
	if ( !isset($udesign_options['c2_timeout']) ) {
		$udesign_options['c2_timeout'] = $timeout;
	}
	if ( !isset($udesign_options['c2_text_color']) ) {
		$udesign_options['c2_text_color'] = $text_color;
	}
	if ( !isset($udesign_options['c2_text_size']) ) {
		$udesign_options['c2_text_size'] = $text_size;
	}
	if ( !isset($udesign_options['c2_slider_text_line_height']) ) {
		$udesign_options['c2_slider_text_line_height'] = $text_line_height;
	}

	update_option('udesign_options',$udesign_options);
}
add_action('save_post', 'ls_slider_save_post');

/* deleting post/meta information and removing udesign slider info */
function ls_slider_delete_post( $post_id ) {
	if (!checkPermissions()) {
		return;
	}

	global $udesign_options;
	$udesign_options = get_option('udesign_options');
	$c2_slides_order_str = $udesign_options['c2_slides_order_str'];
	$c2_slides_array = explode( ',', $udesign_options['c2_slides_order_str'] );
	$found = false;
	$counter = 0;
	foreach ($c2_slides_array as $slide_id) {
		if ($slide_id == $post_id ) {
			unset($udesign_options['c2_slide_img_url_'.$slide_id]);
			unset($udesign_options['c2_transition_type_'.$slide_id]);
			unset($udesign_options['c2_slide_link_url_'.$slide_id]);
			unset($udesign_options['c2_slide_link_target_'.$slide_id]);
			unset($udesign_options['c2_slide_image_alt_tag_'.$slide_id]);
			unset($udesign_options['c2_slide_default_info_txt_'.$slide_id]);
			unset($udesign_options['c2_slide_button_txt_'.$slide_id]);
			unset($udesign_options['c2_slide_button_style_'.$slide_id]);
			unset($udesign_options['c2_speed'.$slide_id]);
			unset($udesign_options['c2_timeout'.$slide_id]);
			unset($udesign_options['c2_text_color'.$slide_id]);
			unset($udesign_options['c2_slider_text_size'.$slide_id]);
			unset($udesign_options['c2_slider_text_line_height'.$slide_id]);
			$found = true;
			break;
		}
		$counter++;
	}
	if ($found) {
		unset($c2_slides_array[$counter]);
	}
	$udesign_options['c2_slides_order_str'] = implode($c2_slides_array,',');
	update_option('udesign_options',$udesign_options);
}
add_action('delete_post', 'ls_slider_delete_post');

/*********** END HOMEPAGE SLIDER METABOX *************/


/********** START ADVANCED POST TYPES ORDER PLUGIN ADDONS *************/
/*********    (recommended but not mandatory)      ***********/
//add custom update field
function ls_apto_order_update($params) {
	$post_id = $params['post_id'];
	$pos = $params['position'];
	$udesign_options = get_option('udesign_options');
	$udesign_options_list = explode(',',$udesign_options['c2_slides_order_str']);
	$udesign_options_list[$pos] = $post_id;
	$udesign_options['c2_slides_order_str'] = implode(',',$udesign_options_list);
	
	update_option('udesign_options',$udesign_options);
}
add_action('apto_order_update', 'ls_apto_order_update');

//for advanced post types order plugin 
function  ls_apto_reorder_item_thumbnail($image_html, $post_ID)
    {
		$udesign_options = get_option('udesign_options');
        //let's use a custom field called "custom_image" which contain the media we need to show for this
        $image_url = $udesign_options['c2_slide_img_url_'.$post_ID];
        if($image_url != '') {
            $image_html = '<img src="'. $image_url .'" style="max-width: 64px;" alt="" />';
		}
        return $image_html;
    }
add_filter('apto_reorder_item_thumbnail', 'ls_apto_reorder_item_thumbnail', 10, 2);
/********** END ADVANCED POST TYPES ORDER PLUGIN ADDONS *************/


/***** START SHORTCODES *******/
//[ls-addon slug="homepage-after-slider"]
function ls_addon_shortcode($atts){
	$slug_title = 'homepage-after-slider';
									
	if ( isset($atts['slug']) ) {
		$slug_title = $atts['slug'];
	}
	$args = array('post_type' => 'page','name' => $slug_title);
	$the_query = new WP_Query( $args );
	//get page
	while ( $the_query->have_posts() ) :
		$the_query->the_post();
		return the_content();
	endwhile;
}

add_shortcode('ls-addon' , 'ls_addon_shortcode');

/***** END SHORTCODES *******/