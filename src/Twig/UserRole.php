<?php

namespace AchttienVijftien\Tile\Twig;

use WP_Role;

/**
 * Class Role.
 */
class UserRole {

	/**
	 * The role.
	 *
	 * @var WP_Role|null
	 */
	private ?WP_Role $role;

	/**
	 * Role constructor.
	 *
	 * @param string $role Role name.
	 */
	public function __construct( string $role, protected User $user ) {
		$this->role = get_role( $role );
	}

	/**
	 * Whether this role has a given capability.
	 *
	 * @param string $cap Capability to check.
	 *
	 * @return bool
	 */
	public function has_cap( string $cap ) {
		return (bool) $this->role?->has_cap( $cap );
	}

	/**
	 * Magic method that defines what will be returned when this class is treated like a string.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return (string) $this->role?->name;
	}
}
