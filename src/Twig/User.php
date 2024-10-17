<?php

namespace AchttienVijftien\Tile\Twig;

use WP_User;

/**
 * Class User.
 */
class User {

	/**
	 * WP_User object.
	 *
	 * @var WP_User|null
	 */
	private ?WP_User $user;

	/**
	 * User meta accessor.
	 *
	 * @var Meta|null
	 */
	private ?Meta $meta;

	/**
	 * User constructor.
	 *
	 * @param mixed $user WP_User or User id.
	 */
	public function __construct( $user ) {
		if ( $user instanceof WP_User ) {
			$this->user = $user;
		} elseif ( is_numeric( $user ) ) {
			$this->user = get_user_by( 'ID', $user ) ?: null;
		}

		if ( ! $this->user ) {
			$this->user = new WP_User( (object) [] );
		}

		$this->meta = new Meta( 'user', $this->user->ID );
	}

	/**
	 * Returns the user ID.
	 *
	 * @return int|null
	 */
	public function id(): ?int {
		return $this->user->ID;
	}

	/**
	 * Returns the user's login name.
	 *
	 * @return string
	 */
	public function username(): string {
		return $this->user->user_login ?? '';
	}

	/**
	 * Returns the user's display name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->user->display_name ?? '';
	}

	/**
	 * Returns the meta object.
	 *
	 * @return Meta|null
	 */
	public function meta(): ?Meta {
		return $this->meta;
	}

	/**
	 * Returns the user's first name.
	 *
	 * @return string
	 */
	public function first_name(): string {
		return $this->meta->first_name[0] ?? '';
	}

	/**
	 * Returns the user's last name.
	 *
	 * @return string
	 */
	public function last_name(): string {
		return $this->meta->last_name[0] ?? '';
	}

	/**
	 * Returns the user's email.
	 *
	 * @return string
	 */
	public function email(): string {
		return $this->user->user_email ?? '';
	}

	/**
	 * Returns the user's url.
	 *
	 * @return string
	 */
	public function url(): string {
		return $this->user->user_url ?? '';
	}

	/**
	 * Returns the user's description.
	 *
	 * @return string
	 */
	public function description(): string {
		return $this->meta->description[0] ?? '';
	}

	/**
	 * Returns the user's author url.
	 *
	 * @return string
	 */
	public function link(): string {
		return $this->user->ID ? get_author_posts_url( $this->user->ID ) : '';
	}

	/**
	 * Returns the user's locale.
	 *
	 * @return string
	 */
	public function locale(): string {
		return $this->user->ID ? get_user_locale( $this->user->ID ) : '';
	}

	/**
	 * Returns the user's nickname.
	 *
	 * @return string
	 */
	public function nickname(): string {
		return $this->meta ? $this->meta->nickname[0] ?? '' : '';
	}

	/**
	 * Returns the user's nicename(slug)
	 *
	 * @return string
	 */
	public function slug(): string {
		return $this->user->user_nicename ?? '';
	}

	/**
	 * Returns the user's registration date.
	 *
	 * @return string
	 */
	public function registered_date(): string {
		return $this->user->user_registered ?? '';
	}

	/**
	 * Returns the user's password.
	 *
	 * @return string
	 */
	public function password(): string {
		return $this->user->user_pass ?? '';
	}

	/**
	 * Returns the user's capabilities.
	 *
	 * @return array
	 */
	public function capabilities(): array {
		return $this->user->allcaps ?? [];
	}

	/**
	 * Returns the user's avatar url.
	 *
	 * @return string
	 */
	public function avatar_url(): string {
		return $this->user->ID ? get_avatar_url( $this->user->ID ) : '';
	}

	/**
	 * Returns the user's post count.
	 *
	 * @param mixed $post_type   the post type(s), any counts all post types.
	 * @param bool  $public_only Whether to only return counts for public posts.
	 *
	 * @return int
	 */
	public function post_count( $post_type = 'any', $public_only = false ): int {
		if ( 'any' !== $post_type ) {
			return $this->user->ID ? (int) count_user_posts( $this->user->ID, $post_type, $public_only ) : 0;
		}

		return (int) count( $this->user_posts( $post_type ) );
	}

	/**
	 * Returns the user's posts.
	 *
	 * @param mixed $post_type the post type(s). Any returns all post types.
	 *
	 * @return int[]|\WP_Post[]
	 */
	private function user_posts( $post_type ): array {
		if ( ! $this->user->ID ) {
			return [];
		}

		return get_posts(
			[
				'author'         => $this->user->ID,
				'post_type'      => $post_type,
				'posts_per_page' => - 1,
			]
		);
	}

	/**
	 * Returns the user's posts.
	 *
	 * @param mixed $post_type the post type(s). Any returns all post types.
	 *
	 * @return int[]|WP_Post[]
	 */
	public function posts( $post_type = 'any' ): array {
		$posts = $this->user_posts( $post_type );

		if ( ! $posts ) {
			return [];
		}

		return array_map(
			function ( $post ) {
				return new Post( $post );
			},
			$posts
		);
	}

	/**
	 * Checks whether the user has a specific role.
	 *
	 * @param string $role The user role.
	 *
	 * @return bool
	 */
	public function has_role( string $role ): bool {
		return in_array( $role, $this->user->roles, true );
	}

	/**
	 * Gets role if user has it.
	 *
	 * @param string $role Role name to get.
	 *
	 * @return UserRole|null
	 */
	public function get_role( string $role ) {
		if ( ! $this->has_role( $role ) ) {
			return null;
		}

		$roles = $this->roles();

		return $roles[ $role ] ?? null;
	}

	/**
	 * Returns the user's roles.
	 *
	 * @return array
	 */
	public function roles(): array {
		$role_classes = apply_filters( 'tile_user_role_class_mapping', [] );

		return array_combine(
			$this->user->roles,
			array_map(
				fn( $role ) => new ( $role_classes[ $role ] ?? UserRole::class )( $role, $this ),
				$this->user->roles ?? []
			)
		);
	}

	/**
	 * Removes role from user.
	 *
	 * @param string $role The role to remove.
	 *
	 * @return bool
	 */
	public function remove_role( string $role ): bool {
		if ( ! $this->has_role( $role ) ) {
			return false;
		}

		$this->user->remove_role( $role );

		return true;
	}
}

