<?php
/**
 * File functions.php
 *
 * @package AchttienVijftien\Tile\Test
 */

namespace AchttienVijftien\Tile\Test;

require ABSPATH . 'tile/vendor/autoload.php';
require ABSPATH . 'tile/test/fixtures/theme/TestContext.php';

use AchttienVijftien\Tile\TemplateLoader;

( new TemplateLoader() )->add_hooks();

/**
 * Setup theme
 *
 * @return void
 */
function theme_setup(): void {
	add_theme_support( 'title-tag' );
}

add_action( 'after_setup_theme', 'AchttienVijftien\Tile\Test\theme_setup' );

/**
 * Adds context to context mapping.
 */
add_filter( 'tile_context_mapping', function ( array $mapping ): array {
	return $mapping + [
			'test' => TestContext::class,
		];
} );
