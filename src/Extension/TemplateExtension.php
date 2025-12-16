<?php
/**
 * TemplateExtension
 *
 * @package AchttienVijftien\Tile\Extension
 */

namespace AchttienVijftien\Tile\Extension;

use AchttienVijftien\Tile\Twig\Menu;
use AchttienVijftien\Tile\Twig\Pagination;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class TemplateExtension
 */
class TemplateExtension extends AbstractExtension {

	/**
	 * Returns twig filters provided by the extension.
	 *
	 * @return TwigFilter[]
	 */
	public function getFilters(): array {
		return [
			new TwigFilter( 'wpautop', 'wpautop', [ 'is_safe' => [ 'html' ] ] ),
		];
	}

	/**
	 * Returns twig functions provided by the extension.
	 *
	 * @return TwigFunction[]
	 */
	public function getFunctions(): array {
		return [
			new TwigFunction( 'wp_head', 'wp_head' ),
			new TwigFunction( 'wp_footer', 'wp_footer' ),
			new TwigFunction( 'do_shortcode', 'do_shortcode', [
				'is_safe' => [ 'html' ],
			] ),
			new TwigFunction( 'get_nav_menu', function ( string $theme_location ) {
				return new Menu( $theme_location );
			} ),
			new TwigFunction( 'action', 'do_action' ),
			new TwigFunction( 'body_class', 'body_class' ),
		];
	}
}
