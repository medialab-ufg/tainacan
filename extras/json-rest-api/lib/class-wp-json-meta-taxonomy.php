<?php

class WP_JSON_Meta_Taxonomy extends WP_JSON_Meta {
	/**
	 * Base route name.
	 *
	 * @var string Route base (e.g. /my-plugin/my-type/(?P<id>\d+)/meta). Must include ID selector.
	 */
	protected $base = '/taxonomies/(?P<taxonomy>[\w-]+)/terms/(?P<id>\d+)/meta';

	/**
	 * Associated object type.
	 *
	 * @var string Type slug ("post" or "user")
	 */
	protected $type = 'term';

	/**
	 * Check that the object can be accessed.
	 *
	 * @param mixed $id Object ID
	 * @return boolean|WP_Error
	 */
	protected function check_object( $id ) {
		$id = (int) $id;
                $term = false;
                $taxonomies = ['socialdb_category_type','socialdb_property_type','socialdb_tag_type'];
                foreach ($taxonomies as $taxonomy) {
                    if(get_term_by('id', $id, $taxonomy)){
                        return true;
                    }
                }

		if ( empty( $id ) || !$term ) {
			return new WP_Error( 'json_taxonomy_invalid_term', __( 'Invalid term ID.' ), array( 'status' => 404 ) );
		}

		return true;
	}

	/**
	 * Add meta to a post.
	 *
	 * Ensures that the correct location header is sent with the response.
	 *
	 * @param int $id Post ID
	 * @param array $data {
	 *     @type string|null $key Meta key
	 *     @type string|null $key Meta value
	 * }
	 * @return bool|WP_Error
	 */
	public function add_meta( $id, $data ) {
		$response = parent::add_meta( $id, $data );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = (object) $response->get_data();

		$response = new WP_JSON_Response();
		$response->header( 'Location', json_url( '/posts/' . $id . '/meta/' . $data->ID ) );
		$response->set_data( $data );
		$response = json_ensure_response( $response );

		return $response;
	}

	/**
	 * Add post meta to post responses.
	 *
	 * Adds meta to post responses for the 'edit' context.
	 *
	 * @param WP_Error|array $data Post response data (or error)
	 * @param array $post Post data
	 * @param string $context Context for the prepared post.
	 * @return WP_Error|array Filtered data
	 */
	public function add_term_meta_data( $data, $term, $context ) {
		if ( $context !== 'edit' || is_wp_error( $data ) ) {
			return $data;
		}

		// Permissions have already been checked at this point, so no need to
		// check again
		$data['term_meta'] = $this->get_all_meta( $term['term_id'] );
		if ( is_wp_error( $data['post_meta'] ) ) {
			return $data['post_meta'];
		}

		return $data;
	}

	/**
	 * Add post meta on post update.
	 *
	 * Handles adding/updating post meta when creating or updating posts.
	 *
	 * @param array $post New post data
	 * @param array $data Raw submitted data
	 * @return array|WP_Error Post data on success, post meta error otherwise
	 */
	public function insert_term_meta( $term, $data ) {
		// Post meta
		if ( ! empty( $data['post_meta'] ) ) {
			$result = $this->handle_inline_meta( $term['term_id'], $data['term_meta'] );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return $term;
	}

	/**
	 * Call protected method from {@see WP_JSON_Posts}.
	 *
	 * WPAPI-1.2 deprecated a bunch of protected methods by moving them to this
	 * class. This proxy method is added to call those methods.
	 *
	 * @param string $method Method name
	 * @param array $args Method arguments
	 * @return mixed Return value from the method
	 */
	public function _deprecated_call( $method, $args ) {
		return call_user_func_array( array( $this, $method ), $args );
	}
}
