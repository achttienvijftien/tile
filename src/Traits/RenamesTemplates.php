<?php
/**
 * RenamesTemplates
 *
 * @package AchttienVijftien\Tile\Traits
 */

namespace AchttienVijftien\Tile\Traits;

/**
 * Trait RenamesTemplates
 */
trait RenamesTemplates {
	/**
	 * Changes the file extension for all templates from .php to .html.twig.
	 *
	 * @param mixed $templates Templates.
	 *
	 * @return mixed
	 */
	public function rename_templates( $templates ) {
		if ( ! is_array( $templates ) ) {
			return $templates;
		}

		$expanded_templates = [];
		foreach ( $templates as $template ) {
			$expanded_templates[] = 'templates/' . str_replace( '.php', '.html.twig', $template );
			$expanded_templates[] = $template;
		}

		return $expanded_templates;
	}
}
