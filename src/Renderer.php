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
		$container   = $this->service_container();
		$auto_reload = $this->auto_reload( $container );

		$options = apply_filters(
			'tile_twig_environment_options',
			[
				'debug'       => 'local' === wp_get_environment_type(),
				'auto_reload' => $auto_reload,
				'cache'       => $this->compiled_template_directory( $container ),
			],
			$template_path
		);

		$this->twig = new Environment(
			new FilesystemLoader(
				[
					get_stylesheet_directory() . "/$template_path",
					get_template_directory() . "/$template_path",
				]
			),
			$options
		);

		$this->add_extensions();

		do_action( 'tile_twig_environment', $this->twig, $template_path );
	}

	/**
	 * Gets the 1815 service container when it is booted and exposes the kernel parameters.
	 *
	 * @return object|null
	 */
	private function service_container(): ?object {
		$container = apply_filters( 'achttienvijftien/container', null );

		if ( is_object( $container )
			&& method_exists( $container, 'hasParameter' )
			&& $container->hasParameter( 'kernel.cache_dir' )
			&& $container->hasParameter( 'kernel.debug' )
		) {
			return $container;
		}

		return null;
	}

	/**
	 * Decides whether templates are checked for changes; always while developing.
	 *
	 * @param object|null $container The service container.
	 *
	 * @return bool
	 */
	private function auto_reload( ?object $container ): bool {
		$developing = $container
			? (bool) $container->getParameter( 'kernel.debug' )
			: 'local' === wp_get_environment_type();

		if ( $developing ) {
			return true;
		}

		if ( defined( 'TILE_TWIG_AUTO_RELOAD' ) ) {
			return (bool) TILE_TWIG_AUTO_RELOAD;
		}

		return null === $container;
	}

	/**
	 * Gets the writable directory for compiled templates, or false for no cache.
	 *
	 * @param object|null $container The service container.
	 *
	 * @return string|false
	 */
	private function compiled_template_directory( ?object $container ): string|false {
		$directory = defined( 'TILE_TWIG_CACHE' ) ? TILE_TWIG_CACHE : true;

		if ( false === $directory ) {
			return false;
		}

		if ( ! is_string( $directory ) || '' === $directory ) {
			if ( null === $container ) {
				return false;
			}

			$cache_dir = $container->getParameter( 'kernel.cache_dir' );
			$directory = untrailingslashit( $cache_dir ) . '/tile-twig';
		}

		if ( ! wp_mkdir_p( $directory ) || ! wp_is_writable( $directory ) ) {
			return false;
		}

		return $directory;
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
	 * @param array  $context  The context to load.
	 *
	 * @return string
	 */
	public function render( string $template, array $context = [] ): string {
		try {
			return $this->twig->load( $template )->render( $context );
		} catch ( LoaderError|RuntimeError|SyntaxError $error ) {
			wp_die(
				esc_html(
					$error->getMessage() .
					' File: ' . ( $error->getSourceContext()?->getPath() ?: '"unknown file"' ) .
					' on line ' . $error->getLine() . '.'
				)
			);
		}
	}
}
