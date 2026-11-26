<?php
/**
 * The WordPress functions Renderer calls, answered from the request description.
 *
 * @package AchttienVijftien\Tile\Test
 * @phpcs:disable WordPress.WP.AlternativeFunctions
 * @phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
 * @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
 */

$GLOBALS['tile_test_hooks'] = [];

/**
 * Registers a filter callback.
 *
 * @param string   $hook_name Filter name.
 * @param callable $callback  Callback.
 *
 * @return void
 */
function add_filter( string $hook_name, callable $callback ): void {
	$GLOBALS['tile_test_hooks'][ $hook_name ][] = $callback;
}

/**
 * Registers an action callback.
 *
 * @param string   $hook_name Action name.
 * @param callable $callback  Callback.
 *
 * @return void
 */
function add_action( string $hook_name, callable $callback ): void {
	add_filter( $hook_name, $callback );
}

/**
 * Runs the filter callbacks over a value.
 *
 * @param string $hook_name Filter name.
 * @param mixed  $value     Value to filter.
 * @param mixed  ...$args   Extra arguments.
 *
 * @return mixed
 */
function apply_filters( string $hook_name, mixed $value, mixed ...$args ): mixed {
	foreach ( $GLOBALS['tile_test_hooks'][ $hook_name ] ?? [] as $callback ) {
		$value = $callback( $value, ...$args );
	}

	return $value;
}

/**
 * Runs the action callbacks.
 *
 * @param string $hook_name Action name.
 * @param mixed  ...$args   Arguments.
 *
 * @return void
 */
function do_action( string $hook_name, mixed ...$args ): void {
	foreach ( $GLOBALS['tile_test_hooks'][ $hook_name ] ?? [] as $callback ) {
		$callback( ...$args );
	}
}

/**
 * Gets the environment type of the request, production when not given.
 *
 * @return string
 */
function wp_get_environment_type(): string {
	return $GLOBALS['tile_test_request']['environment_type'] ?? 'production';
}

/**
 * Gets the theme directory of the request.
 *
 * @return string
 */
function get_stylesheet_directory(): string {
	return $GLOBALS['tile_test_request']['theme_dir'];
}

/**
 * Gets the theme directory of the request, as there is no parent theme.
 *
 * @return string
 */
function get_template_directory(): string {
	return $GLOBALS['tile_test_request']['theme_dir'];
}

/**
 * Removes trailing slashes.
 *
 * @param string $value Path.
 *
 * @return string
 */
function untrailingslashit( string $value ): string {
	return rtrim( $value, '/\\' );
}

/**
 * Creates a directory with its parents.
 *
 * @param string $target Directory.
 *
 * @return bool
 */
function wp_mkdir_p( string $target ): bool {
	return is_dir( $target ) || @mkdir( $target, 0777, true );
}

/**
 * Checks whether a path is writable.
 *
 * @param string $path Path.
 *
 * @return bool
 */
function wp_is_writable( string $path ): bool {
	return is_writable( $path );
}

/**
 * Stops the request with the message on stderr, so the test shows it.
 *
 * @param string $message Message.
 *
 * @return void
 */
function wp_die( string $message ): void {
	fwrite( STDERR, $message );
	exit( 1 );
}
