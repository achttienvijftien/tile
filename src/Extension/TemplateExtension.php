<?php
/**
 * TemplateExtension
 *
 * @package AchttienVijftien\Tile\Extension
 */

namespace AchttienVijftien\Tile\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TemplateExtension
 */
class TemplateExtension extends AbstractExtension {

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
		];
	}
}
