<?php
/**
 * ContextFactory
 *
 * @package AchttienVijftien\Tile\Context
 */

namespace AchttienVijftien\Tile\Context;

use AchttienVijftien\Tile\Exception\InvalidContextClassException;
use AchttienVijftien\Tile\Exception\MissingContextException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ContextFactory
 */
class ContextFactory extends AbstractExtension {

	/**
	 * Returns context attributes for the given context name.
	 *
	 * @return mixed
	 */
	public static function create_context( $context_name, ...$arguments ) {
		$context_mapping = apply_filters( 'tile_context_mapping', [] );

		if ( ! isset( $context_mapping[ $context_name ] ) || ! class_exists( $context_mapping[ $context_name ] ) ) {
			throw new MissingContextException();
		}

		$context_class = $context_mapping[ $context_name ];

		if ( ! in_array( ContextInterface::class, class_implements( $context_class ) ) ) {
			throw new InvalidContextClassException();
		}

		return ( new $context_class( ...$arguments ) )->get_context();
	}
}
