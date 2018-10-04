<?php
/*
| ===================================================
| BUTTON SHORTCODE
| ===================================================
*/
function btn_shortcode( $atts, $content = null ) {
    $a = shortcode_atts( array(
        'href'  =>  '#'
    ), $atts );
 
    return '<a class="button" href="' . esc_attr($a['href']) . '">' . $content . '</a>';
}
add_shortcode( 'button', 'btn_shortcode' );


/*
| ===================================================
| ALLOW SVG UPLOADS TO MEDIA LIBRARY
| ===================================================
*/
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

/*
|====================================================
| REMOVE COMMENTS FROM WORDPRESS INSTALL
|====================================================
*/
// Disable support for comments and trackbacks in post types
function rgc_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if(post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'rgc_disable_comments_post_types_support');

// remove comments link from admin bar
function my_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action( 'wp_before_admin_bar_render', 'my_admin_bar_render' );

// Close comments on the front-end
function rgc_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'rgc_disable_comments_status', 20, 2);
add_filter('pings_open', 'rgc_disable_comments_status', 20, 2);

// Hide existing comments
function rgc_disable_comments_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'rgc_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function rgc_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'rgc_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function rgc_disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url()); exit;
    }
}
add_action('admin_init', 'rgc_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function rgc_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'rgc_disable_comments_dashboard');

/*
| ===================================================
| REMOVE UNWANTED ADMIN LINKS
| ===================================================
*/
add_action( 'admin_bar_menu', 'rgc_remove_customizer', 999 );
function rgc_remove_customizer( $wp_admin_bar ) {
    $wp_admin_bar->remove_menu( 'customize' );
}

add_action('admin_init', 'rgc_remove_submenu', 102);
function rgc_remove_submenu(){
	global $submenu;
	unset($submenu['themes.php'][6]); // remove customize link
	remove_submenu_page( 'themes.php', 'theme-editor.php' );
	remove_submenu_page('plugins.php', 'plugin-editor.php' );
}

function rgc_remove_menus(){
  //remove_menu_page( 'index.php' );                  //Dashboard
  remove_menu_page( 'jetpack' );                    //Jetpack* 
  //remove_menu_page( 'edit.php' );                   //Posts
  //remove_menu_page( 'upload.php' );                 //Media
  //remove_menu_page( 'edit.php?post_type=page' );    //Pages
  remove_menu_page( 'edit-comments.php' );          //Comments
  //remove_menu_page( 'themes.php' );                 //Appearance
  //remove_menu_page( 'plugins.php' );                //Plugins
  //remove_menu_page( 'users.php' );                  //Users
  //remove_menu_page( 'tools.php' );                  //Tools
  //remove_menu_page( 'options-general.php' );        //Settings
}
add_action( 'admin_menu', 'rgc_remove_menus' );

/*
|====================================================
| ADDS SUPPORT FOR WORDPRESS CUSTOM MENUS
| ===================================================
*/
function register_my_menus() {
	register_nav_menus(
		array(
			'header-menu' => __( 'Header Menu' )
			)
	);
}
add_action( 'init', 'register_my_menus' );

/*
|====================================================
| REMOVE UNNEEDED CALLS TO WP-HEAD
| ===================================================
*/
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);


/*
|====================================================
| lOAD CUSTOM JAVASCRIPT
|====================================================
*/
function rm_ready_scripts() {
	wp_enqueue_script(
		'rm_javascript',
		get_template_directory_uri() . '/assets/scripts.min.js',
		array('jquery'),
		'1.0',
		true
	);
}
add_action('wp_enqueue_scripts', 'rm_ready_scripts');

/*
|====================================================
| CUSTOMIZE THE ADMIN FOOTER AREA
|====================================================
*/
function custom_admin_footer() {
	echo 'Website design by <a href="http://rustygeorge.com/#contact">Rusty George Creative</a> &copy; '.date("Y").'. For site support please <a href="http://rustygeorge.com/#contact">contact us</a>.';
}
add_filter('admin_footer_text', 'custom_admin_footer');

/*
|====================================================
| CHANGE EXCERPT LENGTH / MESSAGE
|====================================================
*/

function custom_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more($more) {
    global $post;
	return '&hellip;<br><a class="readmore-link" href="'. get_permalink($post->ID) . '">Continue Reading</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');



/*
|====================================================
| CUSTOM LOGIN LOGO
|====================================================
*/
function custom_login_logo() {
	echo '<style type="text/css">h1 a { background: url('.get_bloginfo('template_directory').'/assets/dist/logo-login.png) 50% 50% no-repeat !important; }</style>';
}
add_action('login_head', 'custom_login_logo');