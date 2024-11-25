<?php
/**
 * TemplateLoader
 *
 * @package AchttienVijftien\Tile
 */

namespace AchttienVijftien\Tile;

use AchttienVijftien\Tile\Twig\Pagination;
use AchttienVijftien\Tile\Twig\Term;
use AchttienVijftien\Tile\Twig\User;
use WP_Term;

/**
 * Class TemplateLoader
 */
class TemplateLoader {

	/**
	 * Renderer instance.
	 *
	 * @var Renderer
	 */
	private Renderer $renderer;

	/**
	 * TemplateLoader constructor.
	 *
	 * @param string $template_path Template path, relative to theme root.
	 */
	public function __construct( string $template_path = 'templates' ) {
		$this->renderer = Renderer::get_instance( $template_path );
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
		add_filter( 'template_include', [ $this, 'template_include' ], 999 );
		add_filter( 'theme_page_templates', [ $this, 'page_templates' ] );
	}

	/**
	 * Changes the file extension for all templates from .php to .html.twig.
	 *
	 * @param mixed $templates Templates.
	 *
	 * @return mixed
	 */
	public function rename_templates( mixed $templates ): mixed {
		if ( is_array( $templates ) ) {
			$expanded_templates = [];
			foreach ( $templates as $template ) {
				$expanded_templates[] = 'templates/' . str_replace( '.php', '.html.twig', $template );
				$expanded_templates[] = $template;
			}
		}

		return $expanded_templates;
	}

	/**
	 * Fires before including the template.
	 *
	 * @param string|mixed $template Template name.
	 *
	 * @return bool|string
	 */
	public function template_include( mixed $template ): bool|string {
		if ( ! str_ends_with( $template, '.html.twig' ) ) {
			return $template;
		}

		if ( is_string( $template ) && ! empty( $template ) ) {
			$this->render_template( $template );

			return false;
		}

		return get_stylesheet_directory() . '/index.php';
	}

	/**
	 * Adds custom Twig page templates.
	 *
	 * @param array $templates Page templates.
	 *
	 * @return array|mixed
	 */
	public function page_templates( $templates ) {
		if ( ! is_array( $templates ) ) {
			return $templates;
		}

		$theme_directory = get_stylesheet_directory() . '/templates';
		$file_extension  = '.html.twig';

		$template_files = [];
		if ( is_dir( $theme_directory ) ) {
			$dir_iterator = new \RecursiveDirectoryIterator( $theme_directory );
			$iterator     = new \RecursiveIteratorIterator( $dir_iterator );

			foreach ( $iterator as $file ) {
				if ( ! $file->isFile() || false === strrpos( $file->getFilename(), $file_extension ) ) {
					continue;
				}

				if ( ! preg_match( '/\{#\s?Template Name:\s?(.*)\s?#\}/mi', $file->openFile(), $header ) ) {
					continue;
				}

				$template_files[ $file->getFilename() ] = trim( $header[1] );
			}
		}

		return array_merge( $templates, $template_files );
	}

	/**
	 * Renders a twig template.
	 *
	 * @param string $template Template name.
	 *
	 * @return void
	 */
	private function render_template( string $template ): void {
		$template_roots = [ get_stylesheet_directory() . '/templates', get_template_directory() . '/templates' ];

		$template = str_replace( $template_roots, '', $template );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- twig does its own escaping
		echo $this->renderer->render( $template, $this->get_globals() );
	}

	/**
	 * Sets global context.
	 *
	 * @return array
	 */
	private function get_globals(): array {
		global $wp_the_query;

		$current_user = wp_get_current_user();

		$globals['query_vars']   = (array) $wp_the_query->query_vars;
		$globals['posts']        = Wrapper::wrap_multiple( $wp_the_query->posts );
		$globals['post']         = $wp_the_query->post ? Wrapper::wrap( $wp_the_query->post ) : null;
		$globals['current_user'] = $current_user->ID ? new User( wp_get_current_user() ) : null;
		$globals['pagination']   = new Pagination( $wp_the_query );

		if ( $wp_the_query->is_single() || $wp_the_query->is_page() ) {
			$globals['more']   = 1;
			$globals['single'] = 1;
		}

		if ( $wp_the_query->is_author() ) {
			$globals['authordata'] = get_userdata( get_queried_object_id() );
		}

		$queried_object = get_queried_object();
		if ( $queried_object instanceof WP_Term ) {
			$globals['term'] = new Term( $queried_object );
		}

		return $globals;
	}
}
