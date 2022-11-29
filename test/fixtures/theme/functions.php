<?php
/**
 * File functions.php
 *
 * @package AchttienVijftien\Tile\Test
 */

namespace AchttienVijftien\Tile\Test;

require ABSPATH . 'tile/vendor/autoload.php';

use AchttienVijftien\Tile\TemplateLoader;

( new TemplateLoader() )->add_hooks();

