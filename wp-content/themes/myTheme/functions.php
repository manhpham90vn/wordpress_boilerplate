<?php

require get_theme_file_path( '/inc/search-route.php' );
require get_theme_file_path( '/inc/like-route.php' );

// custom rest api
function custom_rest() {
	register_rest_field( 'post', 'authorName', array(
		'get_callback' => function () {
			return get_the_author();
		}
	) );

	register_rest_field( 'note', 'userNoteCount', array(
		'get_callback' => function () {
			return count_user_posts( get_current_user_id(), 'note' );
		}
	) );
}

add_action( 'rest_api_init', 'custom_rest' );

// register file js and css
function registerFiles() {
	wp_enqueue_script( 'google_map', '//maps.googleapis.com/maps/api/js?key=AIzaSyAKdEcNmtWWRrNkEfI6Ik37FK1u0Vo_lqY', null, '1.0', true );
	wp_enqueue_script( 'main_js', get_theme_file_uri( '/build/index.js' ), array( 'jquery' ), '1.0', true );
	wp_enqueue_style( 'google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i' );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'main_styles', get_theme_file_uri( '/build/style-index.css' ) );
	wp_enqueue_style( 'extra_styles', get_theme_file_uri( '/build/index.css' ) );

	wp_localize_script( 'main_js', 'data', array(
		'root_url' => get_site_url(),
		'nonce'    => wp_create_nonce( 'wp_rest' )
	) );
}

add_action( 'wp_enqueue_scripts', 'registerFiles' );

// register menu
function feature() {
	register_nav_menu( 'headerMenu', 'Header Menu' );
	register_nav_menu( 'headerMenuLoggedIn', 'Header Menu Logged In' );
	register_nav_menu( 'footerMenu1', 'Footer Menu 1' );
	register_nav_menu( 'footerMenu2', 'Footer Menu 2' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'img', 400, 260, true );
	add_image_size( 'img2', 400, 650, true );
	add_image_size( 'banner', 1500, 350, true );
}

add_action( 'after_setup_theme', 'feature' );

// register custom post type `event` and program
function post_types() {
	// Campus Post Type
	register_post_type( 'campus', array(
		'supports'     => array( 'title', 'editor', 'excerpt' ),
		'rewrite'      => array( 'slug' => 'campuses' ),
		'has_archive'  => true,
		'public'       => true,
		'show_in_rest' => true,
		'labels'       => array(
			'name'          => 'Campuses',
			'add_new_item'  => 'Add New Campus',
			'edit_item'     => 'Edit Campus',
			'all_items'     => 'All Campuses',
			'singular_name' => 'Campus'
		),
		'menu_icon'    => 'dashicons-location-alt'
	) );

	// Event Post Type
	register_post_type( 'event', array(
		'supports'     => array( 'title', 'editor', 'excerpt' ),
		'rewrite'      => array( 'slug' => 'events' ),
		'has_archive'  => true,
		'public'       => true,
		'show_in_rest' => true,
		'labels'       => array(
			'name'          => 'Events',
			'add_new_item'  => 'Add New Event',
			'edit_item'     => 'Edit Event',
			'all_items'     => 'All Events',
			'singular_name' => 'Event'
		),
		'menu_icon'    => 'dashicons-calendar'
	) );

	// Program Post Type
	register_post_type( 'program', array(
		'supports'     => array( 'title', 'editor' ),
		'rewrite'      => array( 'slug' => 'programs' ),
		'has_archive'  => true,
		'public'       => true,
		'show_in_rest' => true,
		'labels'       => array(
			'name'          => 'Programs',
			'add_new_item'  => 'Add New Program',
			'edit_item'     => 'Edit Program',
			'all_items'     => 'All Program',
			'singular_name' => 'Program'
		),
		'menu_icon'    => 'dashicons-awards'
	) );

	// Professor Post Type
	register_post_type( 'professor', array(
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'public'       => true,
		'show_in_rest' => true,
		'labels'       => array(
			'name'          => 'Professors',
			'add_new_item'  => 'Add New Professor',
			'edit_item'     => 'Edit Professor',
			'all_items'     => 'All Professor',
			'singular_name' => 'Professor'
		),
		'menu_icon'    => 'dashicons-welcome-learn-more'
	) );

	// Note Post Type
	register_post_type( 'note', array(
		'capability_type' => 'note',
		'map_meta_cap'    => true,
		'show_in_rest'    => true,
		'supports'        => array( 'title', 'editor' ),
		'public'          => false,
		'show_ui'         => true,
		'labels'          => array(
			'name'          => 'Notes',
			'add_new_item'  => 'Add New Note',
			'edit_item'     => 'Edit Note',
			'all_items'     => 'All Note',
			'singular_name' => 'Note'
		),
		'menu_icon'       => 'dashicons-welcome-write-blog'
	) );

	// Like Post Type
	register_post_type( 'like', array(
		'supports'        => array( 'title'),
		'public'          => false,
		'show_ui'         => true,
		'labels'          => array(
			'name'          => 'Likes',
			'add_new_item'  => 'Add New Like',
			'edit_item'     => 'Edit Like',
			'all_items'     => 'All Like',
			'singular_name' => 'Like'
		),
		'menu_icon'       => 'dashicons-heart'
	) );
}

add_action( 'init', 'post_types' );

// adjust query for program & event
function adjust_queries( WP_Query $query ) {
	$today = date( 'Ymd' );

	// for archive-program.php
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'program' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
		$query->set( 'post_per_page', 10 );
	}

	// for archive-event.php
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'event' ) ) {
		$query->set( 'meta_key', 'event_date' );
		$query->set( 'orderby', 'meta_value_num' );
		$query->set( 'order', 'ASC' );
		$query->set( 'meta_query', array(
			array(
				'key'     => 'event_date',
				'compare' => '>=',
				'value'   => $today,
				'type'    => 'numeric'
			)
		) );
	}

	// for archive-campus.php
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'campus' ) ) {
		$query->set( 'posts_per_page', - 1 );
	}
}

add_action( 'pre_get_posts', 'adjust_queries' );

function pageBanner( $args = null ) {
	if ( ! isset( $args["title"] ) ) {
		$args["title"] = get_the_title();
	}

	if ( ! isset( $args["sub_title"] ) ) {
		$args["sub_title"] = get_field( "page_banner_subtitle" );
	}

	if ( ! isset( $args["photo"] ) ) {
		if ( get_field( 'page_banner_background_image' ) and ! is_archive() and ! is_home() ) {
			$args['photo'] = get_field( 'page_banner_background_image' )['sizes']['banner'];
		} else {
			$args['photo'] = get_theme_file_uri( '/images/ocean.jpg' );
		}
	}
	?>
    <div class="page-banner">
        <div class="page-banner__bg-image"
             style="background-image: url(<?php echo $args["photo"] ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args["title"] ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args["sub_title"] ?></p>
            </div>
        </div>
    </div>
	<?php
}

function mapKey( $api ) {
	$api["key"] = "AIzaSyAKdEcNmtWWRrNkEfI6Ik37FK1u0Vo_lqY";

	return $api;
}

add_filter( 'acf/fields/google_map/api', "mapKey" );

// redirect subscriber to homepage
add_action( 'admin_init', 'toHome' );

function toHome() {
	$user = wp_get_current_user();
	if ( count( $user->roles ) == 1 && $user->roles[0] == "subscriber" ) {
		wp_redirect( site_url( '/' ) );
		exit;
	}
}

add_action( 'wp_loaded', 'noAdminBar' );

function noAdminBar() {
	$user = wp_get_current_user();
	if ( count( $user->roles ) == 1 && $user->roles[0] == "subscriber" ) {
		show_admin_bar( false );
	}
}

// custom login screen
add_filter( 'login_headerurl', 'custom_login_header' );

function custom_login_header() {
	return esc_url( site_url( '/' ) );
}

add_action( 'login_enqueue_scripts', 'custom_login_css' );

function custom_login_css() {
	wp_enqueue_style( 'google-font', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i' );
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'main_styles', get_theme_file_uri( '/build/style-index.css' ) );
	wp_enqueue_style( 'extra_styles', get_theme_file_uri( '/build/index.css' ) );
}

add_filter( 'login_headertext', 'custom_login_title' );

function custom_login_title() {
	return get_bloginfo( 'name' );
}

// Force note post to be private
add_filter( 'wp_insert_post_data', 'makeNotePrivate', 10, 2 );

function makeNotePrivate( $data, $post_arr ) {

	if ( $data["post_type"] == "note" ) {

		if ( count_user_posts( get_current_user_id(), 'note' ) > 4 && ! $post_arr["ID"] ) {
			die( "You have reached your note limit" );
		}

		$data["post_content"] = sanitize_textarea_field( $data["post_content"] );
		$data["post_title"]   = sanitize_text_field( $data["post_title"] );
	}

	if ( $data["post_type"] == "note" && $data["post_status"] != "trash" ) {
		$data["post_status"] = "private";
	}

	return $data;
}