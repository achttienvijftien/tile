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
// phpcs:ignore PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket,PEAR.Functions.FunctionCallSignature.MultipleArguments
add_filter( 'tile_context_mapping', function ( array $mapping ): array {
	return $mapping + [
		// phpcs:ignore WordPress.Arrays.ArrayIndentation.ItemNotAligned,Generic.Arrays.ArrayIndent.KeyIncorrect
			'test' => TestContext::class,
	// phpcs:ignore WordPress.Arrays.ArrayIndentation.CloseBraceNotAligned,Generic.Arrays.ArrayIndent.CloseBraceIncorrect
		];
} ); // phpcs:ignore PEAR.Functions.FunctionCallSignature.CloseBracketLine
