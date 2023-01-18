<?php
/**
 * MenuItem
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

/**
 * Class MenuItem
 */
class MenuItem extends Post {

	/**
	 * This menu items children.
	 *
	 * @var array
	 */
	private array $children = [];

	/**
	 * @return Rendered
	 */
	public function title(): Rendered {
		return $this->post->title
			? new Rendered(
				$this->post->title,
				apply_filters( 'nav_menu_item_title', apply_filters( 'the_title', $this->post->title ) )
			)
			: parent::title();
	}

	/**
	 * Classes.
	 *
	 * @return array
	 */
	public function classes(): array {
		$classes   = empty( $this->post->classes ) ? [] : (array) $this->post->classes;
		$classes[] = 'menu-item-' . $this->id();

		return apply_filters( 'nav_menu_css_class', array_filter( $classes ), $this->post );
	}

	/**
	 * Target value.
	 *
	 * @return string
	 */
	public function target(): string {
		return $this->post->target ?? '';
	}

	/**
	 * Rel value.
	 *
	 * @return string
	 */
	public function rel(): string {
		if ( '_blank' === $this->post->target && empty( $this->post->xfn ) ) {
			return 'noopener';
		}

		return $this->post->xfn ?? '';
	}

	/**
	 * URL this menu item links to.
	 *
	 * @return string
	 */
	public function link(): string {
		return $this->post->url ?? '';
	}

	/**
	 * Alias of link().
	 *
	 * @return string
	 */
	public function href(): string {
		return $this->link();
	}

	/**
	 * Adds child, order of adding is preserved.
	 *
	 * @param MenuItem $child_item
	 *
	 * @return void
	 */
	public function add_child( MenuItem $child_item ): void {
		$this->children[] = $child_item;
	}

	/**
	 * Gets children of this menu item.
	 *
	 * @return array
	 */
	public function children(): array {
		return $this->children;
	}
}
