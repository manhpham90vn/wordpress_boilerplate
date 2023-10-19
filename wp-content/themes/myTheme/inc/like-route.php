<?php

add_action( 'rest_api_init', 'likeRoutes' );

function likeRoutes() {
	register_rest_route( 'test_rest/v1', 'like', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'createLike',
		'permission_callback' => '__return_true'
	) );

	register_rest_route( 'test_rest/v1', 'like', array(
		'methods'             => WP_REST_Server::DELETABLE,
		'callback'            => 'deleteLike',
		'permission_callback' => '__return_true'
	) );
}

function createLike( $data ) {
	if ( is_user_logged_in() ) {
		$professor  = sanitize_text_field( $data['id'] );
		$exitsQuery = new WP_Query( array(
			'author'     => get_current_user_id(),
			'post_type'  => 'like',
			'meta_query' => array(
				array(
					'key'     => 'liked_professor_id',
					'compare' => '=',
					'value'   => $professor
				)
			)
		) );
		if ( $exitsQuery->found_posts == 0 && get_post_type( $professor ) == 'professor' ) {
			return wp_insert_post( array(
				'post_type'   => 'like',
				'post_status' => 'publish',
				'meta_input'  => array(
					'liked_professor_id' => $professor
				)
			) );
		} else {
			die( "not allow" );
		}
	} else {
		die( "not allow" );
	}
}

function deleteLike( $data ) {
	$likeId = sanitize_text_field( $data["id"] );
	if ( get_current_user_id() == get_post_field( 'post_author', $likeId ) && get_post_type( $likeId ) == 'like' ) {
		wp_delete_post( $likeId, true );
		return "delete success";
	} else {
		die( "not allow" );
	}

}