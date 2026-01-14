<?php
/**
 * Menu
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

use WP_Term;

/**
 * Class Menu
 */
class Menu {

	/**
	 * @param string $theme_location Theme location.
	 */
	public function __construct( private string $theme_location ) {
	}

	/**
	 * Get menu based on theme location.
	 *
	 * @return WP_Term|null
	 */
	public function get_menu(): ?WP_Term {
		$locations = get_nav_menu_locations();
		if ( empty( $locations[ $this->theme_location ] ) ) {
			return null;
		}

		return wp_get_nav_menu_object( $locations[ $this->theme_location ] ) ?? null;
	}

	/**
	 * Gets all items of this menu.
	 *
	 * @return MenuItem[]|null
	 */
	public function get_items(): ?array {
		$menu = $this->get_menu();
		if ( ! $menu ) {
			return null;
		}

		$nav_items = wp_get_nav_menu_items( $menu, [
			'update_post_term_cache' => false,
		] );

		if ( empty( $nav_items ) ) {
			return null;
		}

		// use WP internal function, this will save a lot of effort.
		_wp_menu_item_classes_by_context( $nav_items );

		$items = [];

		// First pass: create ALL MenuItem objects.
		foreach ( $nav_items as $nav_item ) {
			$items[ $nav_item->ID ] = [
				'object'   => new MenuItem( $nav_item ),
				'parent'   => (int) $nav_item->menu_item_parent,
				'children' => [],
			];
		}

		// Second pass: assign children.
		foreach ( $items as $id => $item ) {
			if ( $item['parent'] && isset( $items[ $item['parent'] ] ) ) {
				$items[ $item['parent'] ]['children'][] = $id;
			}
		}

		// Build tree.
		$tree = [];
		foreach ( $items as $id => $item ) {
			if ( $item['parent'] === 0 ) {
				$tree[ $id ] = $item['object'];
			}

			foreach ( $item['children'] as $child_id ) {
				$item['object']->add_child( $items[ $child_id ]['object'] );
			}
		}

		return array_values( $tree );
	}

}
