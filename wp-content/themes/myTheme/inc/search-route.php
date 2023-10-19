<?php

add_action( 'rest_api_init', 'search_rest_custom' );

function search_rest_custom() {
	register_rest_route( 'test_rest/v1', 'search', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'searchResults',
		'permission_callback' => '__return_true'
	) );
}

function searchResults( $data ) {
	$query  = new WP_Query( array(
		'post_type' => array( 'post', 'page', 'professor', 'program', 'campus', 'event' ),
		's'         => sanitize_text_field( $data['term'] )
	) );
	$result = array(
		'generalInfo' => array(),
		'professors'  => array(),
		'programs'    => array(),
		'events'      => array(),
		'campuses'    => array()
	);

	while ( $query->have_posts() ) {
		$query->the_post();

		if ( get_post_type() == 'post' || get_post_type() == 'page' ) {
			array_push( $result['generalInfo'], array(
				'title'      => get_the_title(),
				'permalink'  => get_the_permalink(),
				'postType'   => get_post_type(),
				'authorName' => get_the_author()
			) );
		}

		if ( get_post_type() == 'professor' ) {
			array_push( $result['professors'], array(
				'title'     => get_the_title(),
				'permalink' => get_the_permalink(),
				'image'     => get_the_post_thumbnail_url( 0, 'img2' ),
			) );
		}

		if ( get_post_type() == 'program' ) {
			array_push( $result['programs'], array(
				'title'     => get_the_title(),
				'permalink' => get_the_permalink(),
				'id'        => get_the_ID()
			) );
		}

		if ( get_post_type() == 'event' ) {
			$data = get_field( 'event_date' );
			$date = DateTime::createFromFormat( 'm/d/Y', $data );
			array_push( $result['events'], array(
				'title'       => get_the_title(),
				'description' => wp_trim_words( get_the_content(), 18 ),
				'permalink'   => get_the_permalink(),
				'date'        => $date->format( 'M' ),
				'month'       => $date->format( 'd' )
			) );
		}

		if ( get_post_type() == 'campus' ) {
			array_push( $result['campuses'], array(
				'title'     => get_the_title(),
				'permalink' => get_the_permalink()
			) );
		}
	}

	if ( $result['programs'] ) {
		$metaQuery = array(
			'relation' => 'OR'
		);

		foreach ( $result['programs'] as $item ) {
			array_push( $metaQuery, array(
				'key'     => 'releted_programs',
				'compare' => 'LIKE',
				'value'   => '"' . $item["id"] . '"'
			) );
		}

		$relation = new WP_Query( array(
			'post_type'  => 'professor',
			'meta_query' => $metaQuery
		) );

		while ( $relation->have_posts() ) {
			$relation->the_post();

			if ( get_post_type() == 'professor' ) {
				array_push( $result['professors'], array(
					'title'     => get_the_title(),
					'permalink' => get_the_permalink(),
					'image'     => get_the_post_thumbnail_url( 0, 'img2' ),
				) );
			}

		}

		$result["professors"] = array_values( array_unique( $result["professors"], SORT_REGULAR ) );
	}

	return $result;
}