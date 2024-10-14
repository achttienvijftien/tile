<?php
/**
 * Wrapper
 */

namespace AchttienVijftien\Tile;

use AchttienVijftien\Tile\Twig\Post;
use WP_Post;

/**
 * Class Wrapper.
 */
class Wrapper {

	/**
	 * Wraps object into Twig object.
	 *
	 * @param mixed $object The object to wrap.
	 *
	 * @return mixed
	 */
	public static function wrap( $object, ?string $object_type = null ) {
		// if object is post.
		if ( ( $object_type && 'post' === $object_type ) || $object instanceof WP_Post ) {
			// if object is int, convert into WP_Post.
			if ( is_numeric( $object ) ) {
				$object = get_post( $object );
			}

			$class_map = apply_filters( 'tile_post_type_class_mapping', [] );
			$class     = $class_map[ $object->post_type ] ?? Post::class;

			return new $class( $object );
		}

		// if object is taxonomy term.
		if ( ( $object_type && 'term' === $object_type ) || $object instanceof WP_Term ) {
			// if object is int, convert into WP_Term.
			if ( is_numeric( $object ) ) {
				$object = get_term( $object );
			}

			$class_map = apply_filters( 'tile_taxonomy_term_class_mapping', [] );
			$class     = $class_map[ $object->taxonomy ] ?? Term::class;

			return new $class( $object );
		}

		// if object is user.
		if ( ( $object_type && 'user' === $object_type ) || $object instanceof WP_User ) {
			// if object is int, convert into WP_Term.
			if ( is_numeric( $object ) ) {
				$object = get_user_by( 'ID', $object );
			}

			$class_map = apply_filters( 'tile_user_class_mapping', [] );
			$class     = $class_map[ current( $object->roles ) ] ?? User::class;

			return new $class( $object );
		}

		return $object;
	}

	/**
	 * Wraps an array of objects into Twig object.
	 *
	 * @param array $objects Array of objects to wrap.
	 *
	 * @return array
	 */
	public static function wrap_multiple( array $objects, ?string $object_type = null ): array {
		return array_map( fn( $object ) => self::wrap( $object, $object_type ), $objects );
	}

}
