<?php

namespace TenUp\P2P;

use TenUp\P2P\Relationships\PostToPost;
use TenUp\P2P\Relationships\PostToUser;
use TenUp\P2P\Relationships\Relationship;

/**
 * Creates and Tracks any relationships between post types
 */
class Registry {

	protected $post_post_relationships = array();

	protected $post_user_relationships = array();

	public function setup() {}

	/**
	 * Gets a key that uniquely identifies a relationship between two CPTs
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $type
	 *
	 * @return string
	 */
	public function get_relationship_key( $from, $to, $type ) {
		return "{$from}_{$to}_{$type}";
	}

	/**
	 * Checks if a relationship exists between two post types.
	 *
	 * Order of post type doesn't matter when checking if the relationship already exists.
	 *
	 * @param string $cpt1
	 * @param string $cpt2
	 * @param string $type
	 *
	 * @return bool
	 */
	public function post_to_post_relationship_exists( $cpt1, $cpt2, $type ) {
		$relationship = $this->get_post_to_post_relationship( $cpt1, $cpt2, $type );

		if ( ! $relationship ) {
			return false;
		}

		return true;
	}

	public function get_post_to_post_relationship_by_key( $key ) {
		if ( isset( $this->post_post_relationships[ $key ] ) ) {
			return $this->post_post_relationships[ $key ];
		}

		return false;
	}

	/**
	 * Returns the relationship object for the post types provided. Order of CPT args is unimportant.
	 *
	 * @param string $cpt1
	 * @param string $cpt2
	 * @param string $type
	 *
	 * @return bool|Relationship Returns relationship object if relationship exists, otherwise false
	 */
	public function get_post_to_post_relationship( $cpt1, $cpt2, $type ) {
		$key = $this->get_relationship_key( $cpt1, $cpt2, $type );

		$relationship = $this->get_post_to_post_relationship_by_key( $key );

		if ( $relationship ) {
			return $relationship;
		}

		// Try the inverse
		$key = $this->get_relationship_key( $cpt2, $cpt1, $type );

		$relationship = $this->get_post_to_post_relationship_by_key( $key );

		return $relationship;
	}

	/**
	 * Defines a new many to many relationship between two post types
	 *
	 * @param string $from
	 * @param string $to
	 * @param string $type
	 *
	 * @throws \Exception
	 *
	 * @return Relationship
	 */
	public function define_post_to_post( $from, $to, $type, $args = array() ) {
		if ( $this->post_to_post_relationship_exists( $from, $to, $type ) ) {
			throw new \Exception( "A relationship already exists between {$from} and {$to} for type {$type}" );
		}

		$key = $this->get_relationship_key( $from, $to, $type );

		$this->post_post_relationships[ $key ] = new PostToPost( $from, $to, $type, $args );
		$this->post_post_relationships[ $key ]->setup();

		return $this->post_post_relationships[ $key ];
	}

	/* POST TO USER RELATIONSHIPS */

	/**
	 * Checks if a relationship exists between a post type and users
	 *
	 * @param string $post_type
	 * @param string $type
	 *
	 * @return bool
	 */
	public function post_to_user_relationship_exists( $post_type, $type ) {
		$relationship = $this->get_post_to_user_relationship( $post_type, $type );

		if ( ! $relationship ) {
			return false;
		}

		return true;
	}

	public function get_post_to_user_relationship_by_key( $key ) {
		if ( isset( $this->post_user_relationships[ $key ] ) ) {
			return $this->post_user_relationships[ $key ];
		}

		return false;
	}

	/**
	 * Returns the relationship object between users and the post type provided.
	 *
	 * @param string $post_type
	 * @param string $type
	 */
	public function get_post_to_user_relationship( $post_type, $type ) {
		$key = $this->get_relationship_key( $post_type, 'user', $type );

		return $this->get_post_to_user_relationship_by_key( $key );
	}

	/**
	 * Defines a new many to many relationship between users and a post type
	 *
	 * @param string $post_type
	 * @param string $type
	 * @param array $args
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function define_post_to_user( $post_type, $type, $args = array() ) {
		if ( $this->post_to_user_relationship_exists( $post_type, $type ) ) {
			throw new \Exception( "A relationship already exists between users and post type {$post_type} for type {$type}" );
		}

		$key = $this->get_relationship_key( $post_type, 'user', $type );

		$this->post_user_relationships[ $key ] = new PostToUser( $post_type, $type, $args );
		$this->post_user_relationships[ $key ]->setup();

		return $this->post_user_relationships[ $key ];
	}

}
