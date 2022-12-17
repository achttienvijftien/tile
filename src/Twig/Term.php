<?php
/**
 * Term
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

use WP_Error;
use WP_Term;

/**
 * Class Term
 */
class Term {

	/**
	 * WP_Term object.
	 *
	 * @var WP_Term|null
	 */
	private ?WP_Term $term;

	/**
	 * Term meta accessor.
	 *
	 * @var Meta
	 */
	private Meta $meta;

	/**
	 * Term constructor.
	 *
	 * @param mixed $term WP_Term or Term ID.
	 */
	public function __construct( mixed $term ) {
		if ( $term instanceof WP_Term ) {
			$this->term = $term;
		} elseif ( is_numeric( $term ) ) {
			$this->term = get_term( $term );
		} else {
			$this->term = new WP_Term( (object) [] );
		}

		$this->meta = new Meta( 'term', $this->term->term_id );
	}

	/**
	 * Returns the term ID.
	 *
	 * @return int|null
	 */
	public function id(): ?int {
		return $this->term->term_id;
	}

	/**
	 * Returns the post count.
	 *
	 * @return int
	 */
	public function count(): int {
		return (int) $this->term->count;
	}

	/**
	 * Returns the description.
	 *
	 * @return string
	 */
	public function description(): string {
		return (string) $this->term->description;
	}

	/**
	 * Returns the link to the terms archive page.
	 *
	 * @return string|null
	 */
	public function link(): ?string {
		$link = get_term_link( $this->term );

		return ! $link instanceof WP_Error ? (string) $link : null;
	}

	/**
	 * Returns the terms name.
	 *
	 * @return string
	 */
	public function name(): string {
		return (string) $this->term->name;
	}

	/**
	 * Returns the terms slug.
	 *
	 * @return string
	 */
	public function slug(): string {
		return (string) $this->term->slug;
	}

	/**
	 * Returns the terms taxanomy.
	 *
	 * @return string
	 */
	public function taxonomy(): string {
		return (string) $this->term->taxonomy;
	}

	/**
	 * Returns the terms parent, if any.
	 *
	 * @return Term|null
	 */
	public function parent(): ?Term {
		return $this->term->parent ? new Term( $this->term->parent ) : null;
	}

	/**
	 * Returns the meta object.
	 *
	 * @return Meta
	 */
	public function meta(): Meta {
		return $this->meta;
	}
}
