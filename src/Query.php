<?php
/**
 * Query
 */

namespace AchttienVijftien\Tile;

use AchttienVijtien\Tile\Wrapper;

/**
 * Class Query.
 */
class Query {
	public static function get_posts( array $args ) {
		return Wrapper::wrap_multiple( get_posts( $args ), 'post' );
	}
}
