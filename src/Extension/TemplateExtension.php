<?php

namespace AchttienVijftien\Tile\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplateExtension extends AbstractExtension {
	public function getFunctions() {
		return [
			new TwigFunction( 'wp_head', 'wp_head' ),
			new TwigFunction( 'wp_footer', 'wp_footer' ),
		];
	}
}
