<?php
/**
 * Pagination
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

use WP_Query;

/**
 * Class Pagination
 */
class Pagination {
	/**
	 * Page cache.
	 *
	 * @var array
	 */
	private array $page_cache = [];

	/**
	 * Pages object.
	 *
	 * @var Pages
	 */
	private Pages $pages;

	/**
	 * Constructor.
	 *
	 * @param WP_Query $wp_query the wp query.
	 */
	public function __construct( private WP_Query $wp_query ) {
		$this->pages = new Pages( $this );
	}

	/**
	 * Returns the max number of pages.
	 *
	 * @return int
	 */
	public function max_num_pages(): int {
		return $this->query->max_num_pages ?? 1;
	}

	/**
	 * Returns true or false depending on if the page is a term archive or not.
	 *
	 * @return bool
	 */
	private function is_tax(): bool {
		return $this->query->is_tax;
	}

	/**
	 * Returns true or false depending on if the page is a post type archive or not.
	 *
	 * @return bool
	 */
	private function is_post_type_archive(): bool {
		return $this->query->is_post_type_archive;
	}

	/**
	 * Returns the page post type.
	 *
	 * @return string
	 */
	private function post_type(): string {
		return $this->query->query['post_type'] ?? '';
	}

	/**
	 * Returns the current page number.
	 *
	 * @return int
	 */
	public function paged(): int {
		return $this->query->query_vars['paged'] ? (int) $this->query->query_vars['paged'] : 1;
	}

	/**
	 * Returns the pagination base.
	 *
	 * @return string
	 */
	private function pagination_base(): string {
		global $wp_rewrite;

		return $wp_rewrite->pagination_base;
	}

	/**
	 * Returns the next page.
	 *
	 * @return Page|null
	 */
	public function next(): ?Page {
		$next_page = $this->paged() + 1;

		if ( $next_page > $this->max_num_pages() ) {
			return null;
		}

		return $this->page( $next_page );
	}

	/**
	 * Returns the previous page
	 *
	 * @return Page|null
	 */
	public function previous(): ?Page {
		$previous_page = $this->paged() - 1;

		if ( $previous_page <= 0 ) {
			return null;
		}

		return $this->page( $previous_page );
	}

	/**
	 * Returns the page base url
	 *
	 * @param int $page_number the page number for paged.
	 *
	 * @return string
	 */
	public function url( int $page_number = 1 ): string {
		if ( $this->is_tax() ) {
			$url = get_term_link( $this->query->queried_object->term_id );
		} elseif ( $this->is_post_type_archive() ) {
			$url = get_post_type_archive_link( $this->post_type() );
		}

		if ( empty( $url ) || ! is_string( $url ) ) {
			return '';
		}

		return rtrim( $url, '/' ) . '/' . $this->pagination_base() . '/' . $page_number;
	}

	/**
	 * Returns the pages.
	 *
	 * @return Pages
	 */
	public function pages(): Pages {
		return $this->pages;
	}

	/**
	 * Returns page for $page_number.
	 *
	 * @param int $page_number Page number.
	 *
	 * @return Page
	 */
	public function page( int $page_number ): Page {
		if ( ! isset( $this->page_cache[ $page_number ] ) ) {
			$this->page_cache[ $page_number ] = new Page(
				[
					'number'  => $page_number,
					'link'    => $this->url( $page_number ),
					'current' => $page_number === $this->paged(),
				]
			);
		}

		return $this->page_cache[ $page_number ];
	}
}
