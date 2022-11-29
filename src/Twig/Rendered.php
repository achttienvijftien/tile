<?php
/**
 * Rendered
 *
 * @package AchttienVijftien\Tile
 */

namespace AchttienVijftien\Tile\Twig;

use Twig\Markup;

/**
 * Class Rendered
 */
class Rendered extends Markup {

	/**
	 * Raw value.
	 *
	 * @var string
	 */
	private string $raw;

	/**
	 * Rendered constructor.
	 *
	 * @param string $raw      Raw value.
	 * @param string $rendered Rendered value.
	 */
	public function __construct( string $raw, string $rendered ) {
		parent::__construct( $rendered, get_option( 'blog_charset' ) );
		$this->raw = $raw;
	}

	/**
	 * Get raw (unrendered) value.
	 *
	 * @return string
	 */
	public function raw(): string {
		return $this->raw;
	}

	/**
	 * Get rendered value.
	 *
	 * @return Rendered
	 */
	public function rendered(): Rendered {
		return $this;
	}

}
