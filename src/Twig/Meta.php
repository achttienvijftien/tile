<?php
/**
 * Meta
 *
 * @package AchttienVijftien\Tile\Twig
 */

namespace AchttienVijftien\Tile\Twig;

/**
 * Class Meta
 */
class Meta {

	/**
	 * Object type.
	 *
	 * @var string
	 */
	private string $object_type;
	/**
	 * Object subtype.
	 *
	 * @var string
	 */
	private string $object_subtype;
	/**
	 * Object ID.
	 *
	 * @var int
	 */
	private int $object_id;
	/**
	 * Meta key data.
	 *
	 * @var array
	 */
	private array $meta_key_data = [];

	/**
	 * Meta constructor.
	 *
	 * @param string $object_type Object type (post, user, term).
	 * @param int $object_id Object ID.
	 */
	public function __construct( string $object_type, int $object_id ) {
		$this->object_type    = $object_type;
		$this->object_id      = $object_id;
		$this->object_subtype = get_object_subtype( $object_type, $object_id );
	}

	/**
	 * Overloading isset(); always returns true.
	 *
	 * @param string $meta_key Meta key.
	 *
	 * @return bool
	 * @todo: find and implement a better way to check whether a meta key exists.
	 *
	 */
	public function __isset( string $meta_key ): bool {
		return true;
	}

	/**
	 * Get meta value by key.
	 *
	 * @param string $meta_key Meta key.
	 *
	 * @return mixed
	 */
	public function __get( string $meta_key ) {
		$value = get_metadata( $this->object_type, $this->object_id, $meta_key );
		$meta_key_data = $this->get_meta_key_data( $meta_key );
		$single        = boolval( $meta_key_data['single'] ?? false );

		if ( $single ) {
			return is_array( $value ) && isset( $value[0] ) ? $value[0] : null;
		}

		return is_array( $value ) ? $value : [];
	}

	/**
	 * Get meta key data for registered metadata.
	 *
	 * @param string $meta_key Meta key.
	 *
	 * @return string[]|null
	 */
	private function get_meta_key_data( string $meta_key ): ?array {
		if ( key_exists( $meta_key, $this->meta_key_data ) ) {
			return $this->meta_key_data[ $meta_key ];
		}

		$meta_keys = [];

		if ( ! empty( $this->object_subtype ) ) {
			$meta_keys = get_registered_meta_keys( $this->object_type, $this->object_subtype );
		}
		if ( ! isset( $meta_keys[ $meta_key ] ) ) {
			$meta_keys = get_registered_meta_keys( $this->object_type );
		}

		$this->meta_key_data[ $meta_key ] = $meta_keys[ $meta_key ] ?? null;

		return $this->meta_key_data[ $meta_key ];
	}

}
