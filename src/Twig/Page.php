<?php
/**
 * Page
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

/**
 * Page
 */
class Page {
	/**
	 * Page.
	 *
	 * @var array
	 */
	private array $page;

	/**
	 * Post constructor.
	 *
	 * @param array $page the page.
	 */
	public function __construct( array $page ) {
		$this->page = $page;
	}

	/**
	 * Returns the page link
	 *
	 * @return string
	 */
	public function link(): string {
		return $this->page['link'] ?? '';
	}

	/**
	 * Returns the page number
	 *
	 * @return int
	 */
	public function number(): int {
		return $this->page['number'] ?? 1;
	}

	/**
	 * Returns true if the page is the current page.
	 *
	 * @return bool
	 */
	public function current(): bool {
		return boolval( $this->page['current'] ?? false );
	}
}
