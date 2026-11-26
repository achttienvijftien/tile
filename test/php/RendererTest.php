<?php
/**
 * RendererTest
 *
 * @package AchttienVijftien\Tile\Test
 * @phpcs:disable Squiz.Commenting.FunctionComment.Missing
 * @phpcs:disable WordPress.WP.AlternativeFunctions
 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec
 */

namespace AchttienVijftien\Tile\Test;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Each request runs in its own PHP process, as a compiled template class stays loaded.
 */
class RendererTest extends TestCase {

	/**
	 * Temporary directory holding the theme and var/cache of one test.
	 *
	 * @var string
	 */
	private string $dir;

	protected function setUp(): void {
		$this->dir = sys_get_temp_dir() . '/tile-renderer-test-' . uniqid();

		mkdir( $this->dir . '/theme/templates', 0777, true );
		mkdir( $this->dir . '/var/cache', 0777, true );
		file_put_contents( $this->template(), 'Row {{ row }}' );
	}

	protected function tearDown(): void {
		chmod( $this->dir . '/var/cache', 0777 );

		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ( $files as $file ) {
			$file->isDir() ? rmdir( $file->getPathname() ) : unlink( $file->getPathname() );
		}
		rmdir( $this->dir );
	}

	public function test_container_site_compiles_template_once_into_tile_twig(): void {
		$result = $this->request(
			[
				'container' => $this->container( debug: false ),
				'renders'   => 25,
			]
		);

		$this->assertSame( 'Row 24', $result['output'] );
		$this->assertSame( $this->tile_twig_dir(), $result['cache'] );
		$this->assertFalse( $result['auto_reload'] );
		$this->assertCount( 1, $this->compiled_templates( $this->tile_twig_dir() ) );
	}

	public function test_production_keeps_serving_the_compiled_template_after_a_change(): void {
		$this->request( [ 'container' => $this->container( debug: false ) ] );
		$this->change_template();

		$result = $this->request( [ 'container' => $this->container( debug: false ) ] );

		$this->assertSame( 'Row 0', $result['output'] );
	}

	public function test_development_recompiles_a_changed_template(): void {
		$this->request( [ 'container' => $this->container( debug: true ) ] );
		$this->change_template();

		$result = $this->request( [ 'container' => $this->container( debug: true ) ] );

		$this->assertTrue( $result['auto_reload'] );
		$this->assertSame( 'Changed 0', $result['output'] );
	}

	public function test_auto_reload_constant_recompiles_on_a_production_container_site(): void {
		$request = [
			'container' => $this->container( debug: false ),
			'constants' => [ 'TILE_TWIG_AUTO_RELOAD' => true ],
		];

		$this->request( $request );
		$this->change_template();
		$result = $this->request( $request );

		$this->assertTrue( $result['auto_reload'] );
		$this->assertSame( 'Changed 0', $result['output'] );
	}

	public function test_auto_reload_constant_is_ignored_while_developing(): void {
		$result = $this->request(
			[
				'container' => $this->container( debug: true ),
				'constants' => [ 'TILE_TWIG_AUTO_RELOAD' => false ],
			]
		);

		$this->assertTrue( $result['auto_reload'] );
	}

	public function test_site_without_container_renders_uncached(): void {
		$result = $this->request( [] );

		$this->assertSame( 'Row 0', $result['output'] );
		$this->assertFalse( $result['cache'] );
		$this->assertTrue( $result['auto_reload'] );
		$this->assertDirectoryDoesNotExist( $this->tile_twig_dir() );
	}

	public function test_local_without_container_reloads_and_debugs(): void {
		$result = $this->request( [ 'environment_type' => 'local' ] );

		$this->assertTrue( $result['auto_reload'] );
		$this->assertTrue( $result['debug'] );
	}

	public function test_cache_constant_names_the_directory_without_container(): void {
		$directory = $this->dir . '/custom';

		$result = $this->request( [ 'constants' => [ 'TILE_TWIG_CACHE' => $directory ] ] );

		$this->assertSame( $directory, $result['cache'] );
		$this->assertCount( 1, $this->compiled_templates( $directory ) );
	}

	public function test_cache_constant_false_switches_the_cache_off(): void {
		$result = $this->request(
			[
				'container' => $this->container( debug: false ),
				'constants' => [ 'TILE_TWIG_CACHE' => false ],
			]
		);

		$this->assertFalse( $result['cache'] );
		$this->assertDirectoryDoesNotExist( $this->tile_twig_dir() );
	}

	public function test_unwritable_cache_dir_renders_uncached(): void {
		if ( function_exists( 'posix_geteuid' ) && 0 === posix_geteuid() ) {
			$this->markTestSkipped( 'root can write to a read-only directory.' );
		}

		chmod( $this->dir . '/var/cache', 0555 );

		$result = $this->request( [ 'container' => $this->container( debug: false ) ] );

		$this->assertSame( 'Row 0', $result['output'] );
		$this->assertFalse( $result['cache'] );
	}

	public function test_environment_hooks_receive_template_path_and_environment(): void {
		$result = $this->request( [ 'options' => [ 'strict_variables' => true ] ] );

		$this->assertSame( 'templates', $result['filtered_template_path'] );
		$this->assertTrue( $result['strict_variables'] );
		$this->assertTrue( $result['received_environment'] );
	}

	private function request( array $request ): array {
		$request += [ 'theme_dir' => $this->dir . '/theme' ];

		$command = implode(
			' ',
			[
				escapeshellarg( PHP_BINARY ),
				'-d ' . escapeshellarg( 'error_reporting=E_ALL & ~E_DEPRECATED' ),
				'-d display_errors=stderr',
				escapeshellarg( dirname( __DIR__ ) . '/fixtures/renderer/request.php' ),
				escapeshellarg( json_encode( $request ) ),
			]
		);

		exec( $command . ' 2>&1', $output, $exit_code );

		$this->assertSame( 0, $exit_code, implode( "\n", $output ) );

		return json_decode( end( $output ), true );
	}

	private function container( bool $debug ): array {
		return [
			'kernel.cache_dir' => $this->dir . '/var/cache',
			'kernel.debug'     => $debug,
		];
	}

	private function tile_twig_dir(): string {
		return $this->dir . '/var/cache/tile-twig';
	}

	private function template(): string {
		return $this->dir . '/theme/templates/row.html.twig';
	}

	private function change_template(): void {
		file_put_contents( $this->template(), 'Changed {{ row }}' );
		touch( $this->template(), time() + 10 );
		clearstatcache();
	}

	private function compiled_templates( string $directory ): array {
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $directory, FilesystemIterator::SKIP_DOTS )
		);

		return array_filter(
			iterator_to_array( $files ),
			fn( $file ) => 'php' === $file->getExtension()
		);
	}
}
