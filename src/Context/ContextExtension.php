<?php
/**
 * ContextExtension
 *
 * @package AchttienVijftien\Tile\Context
 */

namespace AchttienVijftien\Tile\Context;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ContextExtension
 */
class ContextExtension extends AbstractExtension {

	/**
	 * Returns twig functions provided by the extension.
	 *
	 * @return TwigFunction[]
	 */
	public function getFunctions(): array {
		return [
			new TwigFunction( 'getContext', [ ContextFactory::class, 'create_context' ] ),
		];
	}
}
