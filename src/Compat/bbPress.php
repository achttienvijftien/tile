<?php
/**
 * bbPress
 *
 * @package AchttienVijftien\Tile\Compat
 */

namespace AchttienVijftien\Tile\Compat;

use AchttienVijftien\Tile\Traits\RenamesTemplates;

/**
 * Class bbPress
 */
class bbPress {
	use RenamesTemplates;

	/**
	 * Add hooks.
	 *
	 * @return void
	 */
	public function add_hooks() {
		add_filter( 'bbp_get_profile_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_profile_edit_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_favorites_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_subscriptions_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_single_view_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_single_search_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_single_forum_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_forum_archive_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_forum_edit_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_single_topic_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_topic_archive_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_topic_edit_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_topic_split_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_topic_merge_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_single_reply_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_reply_edit_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_reply_move_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_topic_tag_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_topic_tag_edit_template', [ $this, 'rename_templates' ] );
		add_filter( 'bbp_get_bbpress_template', [ $this, 'rename_templates' ] );
	}

}
