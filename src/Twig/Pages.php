<?php
/**
 * Pages
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

/**
 * Class Pages
 */
class Pages {

	/**
	 * Pages constructor.
	 *
	 * @param Pagination $pagination Pagination.
	 */
	public function __construct( private Pagination $pagination ) {
	}

	/**
	 * Returns the current page.
	 *
	 * @return Page
	 */
	public function current(): Page {
		return $this->pagination->page( $this->pagination->paged() );
	}

	/**
	 * Returns the first $number_of_pages pages.
	 *
	 * @param int $number_of_pages Number of pages to return.
	 *
	 * @return Page|Page[] Page if length = 1, or Page[] if length > 1.
	 */
	public function first( int $number_of_pages = 1 ): Page|array {
		if ( 1 === $number_of_pages ) {
			return $this->pagination->page( 1 );
		}

		return $this->slice( 1, $number_of_pages );
	}

	/**
	 * Returns the last $number_of_pages pages.
	 *
	 * @param int $number_of_pages Number of pages to return.
	 *
	 * @return Page|Page[] Page if length = 1, or Page[] if length > 1.
	 */
	public function last( int $number_of_pages = 1 ): Page|array {
		$max_num_pages = $this->pagination->max_num_pages();

		if ( 1 === $number_of_pages ) {
			return $this->pagination->page( $max_num_pages );
		}

		return $this->slice( - $number_of_pages );
	}

	/**
	 * Returns $number_of_pages pages coming after page $page.
	 *
	 * @param int|Page $page Page before first page to return.
	 * @param int      $number_of_pages Number of pages to return.
	 *
	 * @return array
	 */
	public function after( int|Page $page, int $number_of_pages ): array {
		if ( $number_of_pages < 1 ) {
			return [];
		}

		$after = 0;

		if ( $page instanceof Page ) {
			$after = $page->number();
		}
		if ( is_int( $page ) ) {
			$after = $page;
		}

		if ( $after >= $this->pagination->max_num_pages() ) {
			return [];
		}

		return $this->slice( $after + 1, $number_of_pages );
	}

	/**
	 * Returns $number_of_pages pages coming before page $page.
	 *
	 * @param int|Page $page Page after last page to return.
	 * @param int      $number_of_pages Number of pages to return.
	 *
	 * @return array
	 */
	public function before( int|Page $page, int $number_of_pages ): array {
		$before = $this->pagination->max_num_pages() + 1;

		if ( $page instanceof Page ) {
			$before = $page->number();
		}
		if ( is_int( $page ) ) {
			$before = $page;
		}

		if ( $before < 1 ) {
			return [];
		}

		$start = max( 1, $before - $number_of_pages );

		$number_of_pages = $before - $start;

		if ( $number_of_pages < 1 ) {
			return [];
		}

		return $this->slice( $start, $number_of_pages );
	}

	/**
	 * Returns a slice of pages starting at $start consisting of $length pages.
	 * Follows the same semantics as array_slice.
	 *
	 * @param int      $start Page number range start. Starts at 1.
	 * @param int|null $length Page number range length. Defaults to (max_num_pages - start) + 1.
	 *
	 * @return Page[]
	 * @throws \BadMethodCallException If $start or $length is 0.
	 */
	private function slice( int $start = 1, ?int $length = null ): array {
		$max_num_pages = $this->pagination->max_num_pages();

		if ( 0 === $start ) {
			throw new \BadMethodCallException( 'Invalid argument $start: cannot be 0' );
		}
		if ( 0 === $length ) {
			throw new \BadMethodCallException( 'Invalid argument $length: cannot be 0' );
		}

		if ( $start < 0 ) {
			$start += $max_num_pages + 1;
		}

		if ( $start > $max_num_pages ) {
			return [];
		}

		$start = max( 1, $start );

		$end = $length > 0 ? $start + $length - 1 : $max_num_pages + $length;

		if ( $end < $start ) {
			return [];
		}

		$end = min( $max_num_pages, $end );

		$pages = [];

		for ( $i = $start; $i <= $end; ++ $i ) {
			$pages[] = $this->pagination->page( $i );
		}

		return $pages;
	}
}
