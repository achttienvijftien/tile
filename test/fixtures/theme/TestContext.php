<?php
/**
 * TestContext.
 *
 * @package AchttienVijftien\Tile\Test
 */

namespace AchttienVijftien\Tile\Test;

use AchttienVijftien\Tile\Context\ContextInterface;

/**
 * Class TestContext.
 */
class TestContext implements ContextInterface {
	/**
	 * @inheritDoc
	 */
	public function get_context() {
		return [ 'foo' => 'bar' ];
	}
}
