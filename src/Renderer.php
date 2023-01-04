<?php
/**
 * Renderer
 */

namespace AchttienVijftien\Tile;

use AchttienVijftien\Tile\Context\ContextExtension;
use AchttienVijftien\Tile\Extension\TemplateExtension;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

/**
 * Class Renderer.
 */
class Renderer {

	/**
	 * The instances of Renderer by template path.
	 *
	 * @var array
	 */
	private static array $instances = [];

	/**
	 * Twig instance.
	 *
	 * @var Environment
	 */
	private Environment $twig;

	/**
	 * Gets instance based on template path.
	 *
	 * @param string $template_path Template path the Renderer should use.
	 *
	 * @return Renderer
	 */
	public static function get_instance( string $template_path = 'templates' ): Renderer {
		if ( empty( self::$instances[ $template_path ] ) ) {
			self::$instances[ $template_path ] = new Renderer( $template_path );
		}

		return self::$instances[ $template_path ];
	}

	/**
	 * Renderer constructor.
	 *
	 * @param string $template_path
	 */
	private function __construct( string $template_path ) {
		$this->twig = new Environment(
			new FilesystemLoader(
				[
					get_stylesheet_directory() . "/$template_path",
					get_template_directory() . "/$template_path",
				]
			),
			[ 'debug' => 'local' === wp_get_environment_type() ]
		);

		$this->add_extensions();
	}

	/**
	 * Adds extensions to Twig.
	 *
	 * @return void
	 */
	private function add_extensions(): void {
		// Core Tile extensions.
		$this->twig->addExtension( new DebugExtension() );
		$this->twig->addExtension( new TemplateExtension() );
		$this->twig->addExtension( new ContextExtension() );

		// Third party extensions.
		$extensions = apply_filters( 'tile_twig_extensions', [] );
		foreach ( $extensions as $extension ) {
			if ( ! in_array( ExtensionInterface::class, class_implements( $extension ) ) ) {
				continue;
			}

			$extension_instance = $extension;
			if ( ! is_object( $extension_instance ) ) {
				$extension_instance = new $extension_instance;
			}

			$this->twig->addExtension( $extension_instance );
		}
	}

	/**
	 * Renders given template with given context loaded.
	 *
	 * @param string $template The template to render.
	 * @param array $context The context to load.
	 *
	 * @return string
	 */
	public function render( string $template, array $context = [] ): string {
		try {
			return $this->twig->load( $template )->render( $context );
		} catch ( LoaderError|RuntimeError|SyntaxError $error ) {
			wp_die( esc_html( $error->getMessage() ) );
		}
	}
}
