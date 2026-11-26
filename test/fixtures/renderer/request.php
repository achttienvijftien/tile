<?php
/**
 * Renders a template the way one WordPress request does, and prints the Twig set-up as JSON.
 *
 * @package AchttienVijftien\Tile\Test
 * @phpcs:disable Squiz.Commenting.FunctionComment.Missing
 * @phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
 * @phpcs:disable WordPress.WP.AlternativeFunctions.json_encode_json_encode
 */

use AchttienVijftien\Tile\Renderer;
use Twig\Environment;

require dirname( __DIR__, 3 ) . '/vendor/autoload.php';
require __DIR__ . '/wordpress-functions.php';

$request = json_decode( $argv[1], true );

$GLOBALS['tile_test_request'] = $request;

foreach ( $request['constants'] ?? [] as $constant => $value ) {
	define( $constant, $value );
}

if ( isset( $request['container'] ) ) {
	$container = new class( $request['container'] ) {

		public function __construct( private array $parameters ) {
		}

		public function hasParameter( string $name ): bool {
			return array_key_exists( $name, $this->parameters );
		}

		public function getParameter( string $name ): mixed {
			return $this->parameters[ $name ];
		}
	};

	add_filter( 'achttienvijftien/container', fn() => $container );
}

$filtered_template_path = null;
add_filter(
	'tile_twig_environment_options',
	function ( array $options, string $template_path ) use ( $request, &$filtered_template_path ) {
		$filtered_template_path = $template_path;

		return array_merge( $options, $request['options'] ?? [] );
	}
);

$received_environment = false;
add_action(
	'tile_twig_environment',
	function ( Environment $twig ) use ( &$received_environment ): void {
		$received_environment = $twig instanceof Environment;
	}
);

$output = '';
for ( $row = 0; $row < ( $request['renders'] ?? 1 ); $row++ ) {
	$output = Renderer::get_instance()->render(
		$request['template'] ?? 'row.html.twig',
		[ 'row' => $row ]
	);
}

$twig = ( new ReflectionProperty( Renderer::class, 'twig' ) )->getValue( Renderer::get_instance() );

echo json_encode(
	[
		'output'                 => trim( $output ),
		'cache'                  => $twig->getCache(),
		'auto_reload'            => $twig->isAutoReload(),
		'debug'                  => $twig->isDebug(),
		'strict_variables'       => $twig->isStrictVariables(),
		'filtered_template_path' => $filtered_template_path,
		'received_environment'   => $received_environment,
	]
);
