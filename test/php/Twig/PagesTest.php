<?php
/**
 * PagesTest
 *
 * @package AchttienVijftien\Tile\Test\Twig
 * @phpcs:disable Squiz.Commenting.FunctionComment.Missing
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace AchttienVijftien\Tile\Test\Twig;

use AchttienVijftien\Tile\Twig\Page;
use AchttienVijftien\Tile\Twig\Pages;
use AchttienVijftien\Tile\Twig\Pagination;
use PHPUnit\Framework\TestCase;

/**
 * Class PagesTest
 */
class PagesTest extends TestCase {

	public function test_current(): void {
		$pages   = new Pages( $this->pagination( paged: 2 ) );
		$current = $pages->current();

		$this->assertInstanceOf( Page::class, $current );
		$this->assertEquals( 2, $current->number() );
		$this->assertEquals( 'page/2', $current->link() );
		$this->assertTrue( $current->current() );
	}

	public function test_first(): void {
		$pages = new Pages( $this->pagination( paged: 1, max_num_pages: 2 ) );

		$first = $pages->first();

		$this->assertInstanceOf( Page::class, $first );
		$this->assertEquals( 1, $first->number() );

		$first_two = $pages->first( 3 );
		$this->assertCount( 2, $first_two );
		foreach ( [ 1, 2 ] as $i => $page_number ) {
			$this->assertInstanceOf( Page::class, $first_two[ $i ] );
			$this->assertEquals( $page_number, $first_two[ $i ]->number() );
		}
	}

	public function test_last(): void {
		$pages = new Pages( $this->pagination( paged: 1, max_num_pages: 2 ) );

		$last = $pages->last();

		$this->assertInstanceOf( Page::class, $last );
		$this->assertEquals( 2, $last->number() );

		$last_two = $pages->last( 3 );
		$this->assertCount( 2, $last_two );
		foreach ( [ 1, 2 ] as $i => $page_number ) {
			$this->assertInstanceOf( Page::class, $last_two[ $i ] );
			$this->assertEquals( $page_number, $last_two[ $i ]->number() );
		}
	}

	public function test_after(): void {
		$pages = new Pages( $this->pagination( paged: 1, max_num_pages: 4 ) );

		$after = $pages->after( 2, 5 );

		$this->assertCount( 2, $after );

		foreach ( [ 3, 4 ] as $i => $page_number ) {
			$this->assertInstanceOf( Page::class, $after[ $i ] );
			$this->assertEquals( $page_number, $after[ $i ]->number() );
		}

		$this->assertCount( 1, $pages->after( 0, 1 ) );
		$this->assertCount( 0, $pages->after( 1, 0 ) );
		$this->assertCount( 0, $pages->after( 5, 1 ) );
	}

	public function test_before(): void {
		$pages = new Pages( $this->pagination( paged: 1, max_num_pages: 4 ) );

		$before = $pages->before( 3, 5 );

		$this->assertCount( 2, $before );

		foreach ( [ 1, 2 ] as $i => $page_number ) {
			$this->assertInstanceOf( Page::class, $before[ $i ] );
			$this->assertEquals( $page_number, $before[ $i ]->number() );
		}

		$this->assertCount( 0, $pages->before( 0, 1 ) );
		$this->assertCount( 0, $pages->before( 1, 1 ) );
		$this->assertCount( 0, $pages->before( 2, 0 ) );
		$this->assertCount( 1, $pages->before( 5, 1 ) );
	}

	private function pagination( int $paged, int $max_num_pages = 1 ): Pagination {
		$pagination = $this->createStub( Pagination::class );
		$pagination->method( 'paged' )->willReturn( $paged );
		$pagination->method( 'max_num_pages' )->willReturn( $max_num_pages );
		$pagination->method( 'page' )->willReturnCallback(
			fn( $page_number ) => new Page(
				[
					'number'  => $page_number,
					'link'    => 'page/' . $page_number,
					'current' => $paged === $page_number,
				]
			)
		);

		return $pagination;
	}

}
