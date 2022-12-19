<?php
/**
 * Post
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

use WP_Error;
use WP_Post;

/**
 * Class Post
 */
class Post {

	/**
	 * WP_Post object.
	 *
	 * @var WP_Post|null
	 */
	private ?WP_Post $post;
	/**
	 * Post meta accessor.
	 *
	 * @var Meta
	 */
	private Meta $meta;

	/**
	 * Post constructor.
	 *
	 * @param mixed $post WP_Post or Post ID.
	 */
	public function __construct( mixed $post ) {
		if ( $post instanceof WP_Post ) {
			$this->post = $post;
		} elseif ( is_numeric( $post ) ) {
			$this->post = get_post( $post );
		} else {
			$this->post = new WP_Post( (object) [] );
		}

		$this->meta = new Meta( 'post', $this->post->ID );
	}

	/**
	 * Returns the post ID.
	 *
	 * @return int|null
	 */
	public function id(): ?int {
		return $this->post->ID;
	}

	/**
	 * Returns the post date (local timezone) or null if invalid.
	 *
	 * @return string|null
	 */
	public function date(): ?string {
		return $this->format_date( $this->post->post_date_gmt, $this->post->post_date );
	}

	/**
	 * Returns the post date (GMT timezone) or null if invalid.
	 *
	 * @return string|null
	 */
	public function date_gmt(): ?string {
		if ( '0000-00-00 00:00:00' === $this->post->post_date_gmt ) {
			$post_date_gmt = get_gmt_from_date( $this->post->post_date );
		} else {
			$post_date_gmt = $this->post->post_date_gmt;
		}

		return $this->format_date( $post_date_gmt );
	}

	/**
	 * Returns the GUID.
	 *
	 * @return array
	 */
	public function guid(): array {
		return [
			/** This filter is documented in wp-includes/post-template.php */
			'rendered' => apply_filters( 'get_the_guid', $this->post->guid, $this->post->ID ),
			'raw'      => $this->post->guid,
		];
	}

	/**
	 * Returns the modified date (local timezone) or null if invalid.
	 *
	 * @return string|null
	 */
	public function modified(): ?string {
		return $this->format_date( $this->post->post_modified_gmt, $this->post->post_modified );
	}

	/**
	 * Returns the modified date (GMT timezone) or null if invalid.
	 *
	 * @return string|null
	 */
	public function modified_gmt(): ?string {
		if ( '0000-00-00 00:00:00' === $this->post->post_modified_gmt ) {
			$post_modified_gmt = gmdate(
				'Y-m-d H:i:s',
				strtotime( $this->post->post_modified ) - ( get_option( 'gmt_offset' ) * 3600 )
			);
		} else {
			$post_modified_gmt = $this->post->post_modified_gmt;
		}

		return $this->format_date( $post_modified_gmt );
	}

	/**
	 * Returns the post password.
	 *
	 * @return string
	 */
	public function password(): string {
		return $this->post->post_password;
	}

	/**
	 * Returns the post slug (name).
	 *
	 * @return string
	 */
	public function slug(): string {
		return $this->post->post_name;
	}

	/**
	 * Returns the post status.
	 *
	 * @return string
	 */
	public function status(): string {
		return $this->post->post_status;
	}

	/**
	 * Returns the post type.
	 *
	 * @return string
	 */
	public function type(): string {
		return $this->post->post_type;
	}

	/**
	 * Returns the post permalink.
	 *
	 * @return string
	 */
	public function link(): string {
		return get_post_permalink( $this->post );
	}

	/**
	 * Returns the post title.
	 *
	 * @return Rendered
	 */
	public function title(): Rendered {
		return new Rendered(
			$this->post->post_title,
			get_the_title( $this->post->ID )
		);
	}

	/**
	 * Returns the post content.
	 *
	 * @return Rendered
	 */
	public function content(): Rendered {
		$rendered = '';
		if ( ! post_password_required( $this->post ) ) {
			$rendered = apply_filters( 'the_content', $this->post->post_content );
		}

		return new Rendered(
			$this->post->post_content,
			$rendered
		);
	}

	/**
	 * Returns whether the post is password protected.
	 *
	 * @return bool
	 */
	public function protected(): bool {
		return (bool) $this->post->post_password;
	}

	/**
	 * Returns the excerpt.
	 *
	 * @return Rendered
	 */
	public function excerpt(): Rendered {
		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'get_the_excerpt', $this->post->post_excerpt, $this->post );

		/** This filter is documented in wp-includes/post-template.php */
		$excerpt = apply_filters( 'the_excerpt', $excerpt );

		return new Rendered(
			$this->post->post_excerpt,
			$excerpt
		);
	}

	/**
	 * Returns the author ID or 0 if not set.
	 *
	 * @return int
	 */
	public function author(): int {
		return (int) $this->post->post_author;
	}

	/**
	 * Returns the featured image or null if not set.
	 *
	 * @return Image|null
	 */
	public function featured_media(): ?Image {
		$image_id = (int) get_post_thumbnail_id( $this->post->ID );

		if ( 0 === $image_id ) {
			return null;
		}

		return new Image( $image_id );
	}

	/**
	 * Returns the post parent ID or 0 if not set.
	 *
	 * @return int
	 */
	public function parent(): int {
		return (int) $this->post->post_parent;
	}

	/**
	 * Returns the menu order.
	 *
	 * @return int
	 */
	public function menu_order(): int {
		return (int) $this->post->menu_order;
	}

	/**
	 * Returns the comment status.
	 *
	 * @return string
	 */
	public function comment_status(): string {
		return $this->post->comment_status;
	}

	/**
	 * Returns the ping status.
	 *
	 * @return string
	 */
	public function ping_status(): string {
		return $this->post->ping_status;
	}

	/**
	 * Returns whether the post is sticky or not.
	 *
	 * @return bool
	 */
	public function sticky(): bool {
		return is_sticky( $this->post->ID );
	}

	/**
	 * Returns the page template or '' if not set.
	 *
	 * @return string
	 */
	public function template(): string {
		$template = get_page_template_slug( $this->post->ID );

		return $template ?: '';
	}

	/**
	 * Returns the post format.
	 *
	 * @return string
	 */
	public function format(): string {
		$format = get_post_format( $this->post->ID );
		if ( empty( $format ) ) {
			$format = 'standard';
		}

		return $format;
	}

	/**
	 * Returns the meta object.
	 *
	 * @return Meta
	 */
	public function meta(): Meta {
		return $this->meta;
	}

	/**
	 * Returns the term IDs of associated tags.
	 *
	 * @return array
	 */
	public function tags(): array {
		return $this->terms( 'post_tag' );
	}

	/**
	 * Returns the term IDs of associated categories.
	 *
	 * @return array
	 */
	public function categories(): array {
		return $this->terms( 'category' );
	}

	/**
	 * Get terms in $taxonony associated with post.
	 *
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array
	 */
	public function terms( string $taxonomy ): array {
		$terms = get_the_terms( $this->post, $taxonomy );

		return $terms && ! $terms instanceof WP_Error ? array_map( fn( $term ) => new Term( $term ), $terms ) : [];
	}

	/**
	 * Returns $date_gmt as rfc3339 date, or null if invalid.
	 *
	 * @param string $date_gmt Date (GMT timezone).
	 * @param ?string $date Date (Local timezone).
	 *
	 * @return string|null
	 */
	protected function format_date( string $date_gmt, ?string $date = null ): ?string {
		if ( isset( $date ) ) {
			return mysql_to_rfc3339( $date );
		}

		if ( '0000-00-00 00:00:00' === $date_gmt ) {
			return null;
		}

		return mysql_to_rfc3339( $date_gmt );
	}
}
