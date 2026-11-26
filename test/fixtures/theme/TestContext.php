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
	 * Returns the test context.
	 *
	 * @inheritDoc
	 */
	public function get_context() {
		return [ 'foo' => 'bar' ];
	}
}
