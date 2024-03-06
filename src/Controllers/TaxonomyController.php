<?php

namespace BK\EZCPT\Controllers;

class TaxonomyController {
	private static array $definitions = [];

	public static function init () {
		add_action('init',
			fn () => self::registerTaxonomies());

		add_filter('post_updated_messages',
			fn (array $messages) => self::registerUpdateMessages($messages));
	}

	/**
	 * Preps a custom taxonomy to be registered in WordPress.
	 *
	 * @param array $definition Array of taxonomy properties.
	 *   'slug'      - REQUIRED - string
	 *   'postTypes' - REQUIRED - ['post-type-slug', ...]
	 *   'labels'    - REQUIRED - ['singular label', 'plural label']
	 *   'extra'     - OPTIONAL - array of k-v pairs compatible with `register_taxonomy`
	 */
	public static function register (array $definition) {
		if (
			//@fmt:off
			!array_key_exists('slug', $definition)      || empty($definition['slug'])      ||
			!array_key_exists('postTypes', $definition) || empty($definition['postTypes']) ||
			!array_key_exists('labels', $definition)    || empty($definition['labels'])    ||
			empty($definition['labels'][0])             || empty($definition['labels'][1])
			//@fmt:on
		) {
			// TODO - Warn "insufficient data"
			return;
		}

		if (array_key_exists($definition['slug'], self::$definitions)) {
			// TODO - Warn "already registered"
			return;
		}

		self::$definitions[$definition['slug']] = $definition;
	}

	/**
	 * Actually register the taxonomy with WordPress. Sets up sane defaults,
	 *   applies any extras/overrides, then registers taxonomy.
	 */
	protected static function registerTaxonomies () {
		foreach (self::$definitions as $k => $definition) {
			[0 => $single, 1 => $plural] = $definition['labels'];

			$args = [
				'labels' => [
					'name'                       => "{$plural}",
					'singular_name'              => "{$single}",
					'search_items'               => "Search {$plural}",
					'popular_items'              => "Popular {$plural}",
					'all_items'                  => "All {$plural}",
					'parent_item'                => "Parent {$single}",
					'parent_item_colon'          => "Parent {$single}:",
					'edit_item'                  => "Edit {$single}",
					'update_item'                => "Update {$single}",
					'view_item'                  => "View {$single}",
					'add_new_item'               => "Add New {$single}",
					'new_item_name'              => "New {$single}",
					'separate_items_with_commas' => "Separate {$plural} with commas",
					'add_or_remove_items'        => "Add or remove {$plural}",
					'choose_from_most_used'      => "Choose from the most used {$plural}",
					'not_found'                  => "No {$plural} found.",
					'no_terms'                   => "No {$plural}",
					'menu_name'                  => "{$plural}",
					'items_list_navigation'      => "{$plural} list navigation",
					'items_list'                 => "{$plural} list",
					'most_used'                  => "Most Used",
					'back_to_items'              => "&larr; Back to {$plural}",
				],

				/* TODO - Check capability stuff.
				'capabilities' => [
					'manage_terms' => 'edit_posts',
					'edit_terms'   => 'edit_posts',
					'delete_terms' => 'edit_posts',
					'assign_terms' => 'edit_posts',
				],
				*/

				/* TODO - Check custom rewrite stuff.
				'rewrite' => [
					'slug'         => $rewriteSlug,
					'with_front'   => false,
					'hierarchical' => true,
				],
				*/

				'show_admin_column' => true,

				// TODO - Check what these properties do.
				'public'            => true,
				'show_in_rest'      => true,
			];

			if (array_key_exists('extra', $definition) && !empty($definition['extra'])) {
				$args = array_merge($args, $definition['extra']);
			}

			register_taxonomy($definition['slug'], $definition['postTypes'], $args);
		}
	}

	/**
	 * Set up tailored update messages for our taxonomies.
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	protected static function registerUpdateMessages (array $messages) : array {
		foreach (self::$definitions as $definition) {
			[0 => $single, 1 => $plural] = $definition['labels'];

			$messages[$definition['slug']] = [
				0 => '', // Unused. Messages start at index 1.
				1 => "{$single} added.",
				2 => "{$single} deleted.",
				3 => "{$single} updated.",
				4 => "{$single} not added.",
				5 => "{$single} not updated.",
				6 => "{$plural} deleted.",
			];
		}

		return $messages;
	}
}