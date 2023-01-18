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

		$items_multi_level = [];
		foreach ( $nav_items as $nav_item ) {
			if ( ! isset( $items_multi_level[ $nav_item->ID ] ) ) {
				$items_multi_level[ $nav_item->ID ] = [
					'children'  => [],
					'top_level' => ! $nav_item->menu_item_parent,
				];
			}

			$items_multi_level[ $nav_item->ID ]['object'] = new MenuItem( $nav_item );

			if ( empty( $nav_item->menu_item_parent ) ) {
				continue;
			}

			if ( ! isset( $items_multi_level[ $nav_item->menu_item_parent ] ) ) {
				$items_multi_level[ $nav_item->menu_item_parent ] = [
					'children' => [],
				];
			}

			$items_multi_level[ $nav_item->menu_item_parent ]['children'][] = $nav_item->ID;
		}

		$items_tree = [];
		foreach ( $items_multi_level as $item_id => $item ) {
			$item_object = $item['object'];
			if ( ! empty( $item['top_level'] ) ) {
				$items_tree[ $item_id ] = $item_object;
			}

			if ( empty( $item['children'] ) ) {
				continue;
			}

			foreach ( $item['children'] as $item_child_id ) {
				if ( empty( $items_multi_level[ $item_child_id ]['object'] ) ) {
					continue;
				}
				$item_object->add_child( $items_multi_level[ $item_child_id ]['object'] );
			}
		}

		return array_values( $items_tree );
	}
}
