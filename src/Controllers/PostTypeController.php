<?php

namespace BK\EZCPT\Controllers;

use BK\JSONIFY\Utilities\JSONIFY;

class PostTypeController {
	protected static array $definitions = [];

	public static function init () {
		add_action('init',
			fn () => self::registerPostTypes());

		add_filter('post_updated_messages',
			fn (array $messages) => self::registerUpdateMessages($messages));
	}

	/**
	 * Preps a custom post-type to be registered in WordPress.
	 *
	 * @param array $definition Array of post-type properties.
	 *   'slug'   - REQUIRED - string
	 *   'labels' - REQUIRED - ['singular label', 'plural label']
	 *   'icon'   - OPTIONAL - string TODO
	 *   'extra'  - OPTIONAL - array of k-v pairs compatible with `register_post_type`
	 */
	public static function register (array $definition) {
		if (
			//@fmt:off
			!array_key_exists('slug', $definition)   || empty($definition['slug'])      ||
			!array_key_exists('labels', $definition) || empty($definition['labels'])    ||
			empty($definition['labels'][0])          || empty($definition['labels'][1])
			//@fmt:off
		) {
			// TODO - Warn "insufficient data"
			return;
		}

		if (array_key_exists($definition['slug'], self::$definitions)) {
			// TODO - Warn "already registered"
			return;
		}

		// Saving as slug => [def] to make checking for dupes easy and efficient.
		self::$definitions[$definition['slug']] = $definition;
	}

	/**
	 * Actually register the post-type with WordPress. Sets up sane defaults,
	 *   applies any extras/overrides, then registers post-type.
	 */
	protected static function registerPostTypes () {
		foreach (self::$definitions as $k => $definition) {
			[0 => $single, 1 => $plural] = $definition['labels'];

			$args = [
				//@fmt:off
				'labels' => [
					'name'                     => "{$plural}",
					'name_admin_bar'           => "{$single}",
					'singular_name'            => "{$single}",
					'add_new'                  => "Add New",
					'add_new_item'             => "Add New {$single}",
					'edit_item'                => "Edit {$single}",
					'new_item'                 => "New {$single}",
					'view_item'                => "View {$single}",
					'view_items'               => "View {$plural}",
					'search_items'             => "Search {$plural}",
					'not_found'                => "No {$plural} found",
					'not_found_in_trash'       => "No {$plural} found in trash",
					'parent_item_colon'        => "Parent {$single}:",
					'all_items'                => "All {$plural}",
					'archives'                 => "{$single} Archives",
					'attributes'               => "{$single} Attributes",
					'insert_into_item'         => "Insert into {$single}",
					'uploaded_to_this_item'    => "Uploaded to this {$single}",
					'featured_image'           => "Featured Image",
					'set_featured_image'       => "Set featured image",
					'remove_featured_image'    => "Remove featured image",
					'use_featured_image'       => "Use as featured image",
					'menu_name'                => "{$plural}",
					'filter_items_list'        => "Filter {$plural} list",
					'items_list_navigation'    => "{$plural} list navigation",
					'items_list'               => "{$plural} list",
					'item_published'           => "{$single} published.",
					'item_published_privately' => "{$single} published privately.",
					'item_reverted_to_draft'   => "{$single} reverted to draft.",
					'item_trashed'             => "{$single} trashed.",
					'item_scheduled'           => "{$single} scheduled.",
					'item_updated'             => "{$single} updated.",
					'item_link'                => "{$single} Link",
					'item_link_description'    => "A link to a(n) {$single}.",
				],

				'supports' => [
					'author', // TODO
					'comments',
					'custom-fields',
					'editor',
					'page-attributes', // TODO
					'post-formats', // TODO
					'revisions',
					'thumbnail',
					'title',
				],

				'has_archive'  => true,
				//'menu_icon'    => Assets::getAssetURL($postType['icon']), // TODO - Proper menu icon
				'public'       => true,
				'show_in_rest' => true, // Required if we want to use Block Editor for this post-type.
				//@fmt:on
			];

			if (array_key_exists('extra', $definition) && !empty($definition['extra'])) {
				// Do it this way because array_merge and array_merge_recursive break some values.
				foreach ($definition['extra'] as $kx => $vx) {
					// if key from extra exists in args, and if its value is an array
					if (array_key_exists($kx, $args) && is_array($vx)) {
						$args[$kx] = array_merge($args[$kx], $vx);
					} else {
						$args[$kx] = $vx;
					}
				}
			}

			register_post_type($definition['slug'], $args);
		}
	}

	/**
	 * Set up tailored update messages for our custom post-types.
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	protected static function registerUpdateMessages (array $messages) : array {
		global $post;

		$permalink = get_permalink($post);

		foreach (self::$definitions as $k => $definition) {
			[0 => $single] = $definition['labels'];

			$messages[$definition['slug']] = [
				0  => '', // Unused. Messages start at index 1.
				1  => sprintf(
					'%1$s updated. <a target="_blank" href="%2$s">View %1$s</a>',
					$single,
					esc_url($permalink)
				),
				2  => 'Custom field updated.',
				3  => 'Custom field deleted.',
				4  => "{$single} updated.",
				5  => isset($_GET['revision'])
					? sprintf(
						'%1$s restored to revision from %2$s',
						$single,
						wp_post_revision_title((int) $_GET['revision'], false)
					)
					: false,
				6  => sprintf(
					'%1$s published. <a href="%2$s">View %1$s</a>',
					$single,
					esc_url($permalink)
				),
				7  => "{$single} saved.",
				8  => sprintf(
					'%1$s submitted. <a target="_blank" href="%2$s">Preview %1$s</a>',
					$single,
					esc_url(add_query_arg('preview', 'true', $permalink))
				),
				9  => sprintf(
					'%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %1$s</a>',
					$single,
					date_i18n('M j, Y @ G:i', strtotime($post->post_date)),
					esc_url($permalink)
				),
				10 => sprintf(
					'%1$s draft updated. <a target="_blank" href="%2$s">Preview %1$s</a>',
					$single,
					esc_url(add_query_arg('preview', 'true', $permalink))
				),
			];
		}

		return $messages;
	}
}
