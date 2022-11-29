<?php
/**
 * TemplateLoader
 *
 * @package AchttienVijftien\Tile
 */

namespace AchttienVijftien\Tile;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class TemplateLoader
 */
class TemplateLoader {

	/**
	 * Twig instance.
	 *
	 * @var Environment
	 */
	private Environment $twig;

	/**
	 * TemplateLoader constructor.
	 *
	 * @param string $template_path Template path, relative to theme root.
	 */
	public function __construct( string $template_path = 'templates' ) {
		$this->twig = new Environment(
			new FilesystemLoader(
				[
					get_template_directory() . "/$template_path",
					get_stylesheet_directory() . "/$template_path",
				]
			)
		);
	}

	/**
	 * Add hooks.
	 *
	 * @return void
	 */
	public function add_hooks(): void {
		add_filter( '404_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'archive_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'attachment_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'author_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'category_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'date_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'embed_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'frontpage_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'home_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'index_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'page_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'paged_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'privacypolicy_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'search_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'single_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'singular_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'tag_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'taxonomy_template_hierarchy', [ $this, 'rename_templates' ] );
		add_filter( 'template_include', [ $this, 'template_include' ] );
	}

	/**
	 * Changes the file extension for all templates from .php to .twig.
	 *
	 * @param mixed $templates Templates.
	 *
	 * @return mixed
	 */
	public function rename_templates( mixed $templates ): mixed {
		if ( is_array( $templates ) ) {
			return array_map(
				fn( $template ) => str_replace( '.php', '.twig', $template ),
				$templates
			);
		}

		return $templates;
	}

	/**
	 * Fires before including the template.
	 *
	 * @param string|mixed $template Template name.
	 *
	 * @return bool|string
	 */
	public function template_include( mixed $template ): bool|string {
		if ( is_string( $template ) && ! empty( $template ) ) {
			$this->render_template( $template );

			return false;
		}

		return get_stylesheet_directory() . '/index.php';
	}

	/**
	 * Renders a twig template.
	 *
	 * @param string $template Template name.
	 *
	 * @return void
	 */
	private function render_template( string $template ): void {
		$template_roots = [ get_stylesheet_directory(), get_template_directory() ];

		$template = str_replace( $template_roots, '', $template );

		try {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- twig does its own escaping
			echo $this->twig->load( $template )->render( $this->get_globals() );
		} catch ( LoaderError|RuntimeError|SyntaxError $error ) {
			wp_die( esc_html( $error->getMessage() ) );
		}
	}

	private function get_globals(): array {
		global $wp_the_query;

		$globals['query_vars'] = (array) $wp_the_query->query_vars;
		$globals['posts']      = $wp_the_query->posts;
		$globals['post']       = $wp_the_query->post ?? null;

		if ( $wp_the_query->is_single() || $wp_the_query->is_page() ) {
			$globals['more']   = 1;
			$globals['single'] = 1;
		}

		if ( $wp_the_query->is_author() ) {
			$globals['authordata'] = get_userdata( get_queried_object_id() );
		}

		return $globals;
	}
}
