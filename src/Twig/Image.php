<?php
/**
 * Image
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

/**
 * Class Image
 */
class Image {

	/**
	 * Image constructor.
	 *
	 * @param int $image_id image ID.
	 */
	public function __construct( private int $image_id ) {
	}

	/**
	 * Returns the image src
	 *
	 * @param string $image_size the image size to return.
	 *
	 * @return string
	 */
	public function src( string $image_size = 'thumbnail' ): string {
		$image = $this->get_attachment_image( $this->image_id, $image_size );

		return $image[0] ?? '';
	}

	/**
	 * Return the image width.
	 *
	 * @param string $image_size the image size.
	 *
	 * @return int
	 */
	public function width( string $image_size = 'thumbnail' ): int {
		$image = $this->get_attachment_image( $this->image_id, $image_size );

		return $image[1] ?? 0;
	}

	/**
	 * Returns the image height.
	 *
	 * @param string $image_size the image size.
	 *
	 * @return int
	 */
	public function height( string $image_size = 'thumbnail' ): int {
		$image = $this->get_attachment_image( $this->image_id, $image_size );

		return $image[2] ?? 0;
	}

	/**
	 * Returns the image alt.
	 *
	 * @return string
	 */
	public function alt(): string {
		return get_post_meta(
			$this->image_id,
			'_wp_attachment_image_alt',
			true
		);
	}

	/**
	 * Returns the image title.
	 *
	 * @return string
	 */
	public function title(): string {
		return get_the_title( $this->image_id );
	}

	/**
	 * Returns the full attachment.
	 *
	 * @param int $image_id Image id.
	 * @param string $size Image size.
	 * @param bol $icon Whether the image should fall back to a mime type icon
	 *
	 * @return array
	 */
	private function get_attachment_image( int $image_id, string $size, bool $icon = false ): array {
		if ( ! $this->is_available_image_size( $size ) ) {
			return [];
		}

		$image_attachment = wp_get_attachment_image_src( $image_id, $size, $icon );

		if ( ! $image_attachment ) {
			return [];
		}

		return $image_attachment;
	}

	/**
	 * Checks if the given image_size is available on the website.
	 *
	 * @param string $image_size the image size.
	 *
	 * @return bool
	 */
	private function is_available_image_size( string $image_size ): bool {
		return in_array( $image_size, get_intermediate_image_sizes(), true );
	}
}
