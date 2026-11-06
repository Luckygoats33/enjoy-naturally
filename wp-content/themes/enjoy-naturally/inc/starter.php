<?php
/**
 * One-click starter import and the WP-CLI seed command.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

add_action('admin_menu', 'en_starter_menu');
add_action('admin_init', 'en_starter_handle_post');
add_action('admin_notices', 'en_starter_admin_notice');

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::add_command(
		'enjoy-naturally seed',
		static function ( $args, $assoc ) {
			unset($args);
			$result = en_import_starter(
				array(
					'replace'     => isset($assoc['replace']),
					'discourage'  => ! isset($assoc['keep-indexing']),
					'permalinks'  => ! isset($assoc['skip-permalinks']),
					'retitle'     => ! isset($assoc['skip-title']),
				)
			);
			if (is_wp_error($result)) {
				WP_CLI::error($result->get_error_message());
			}
			foreach (array( 'created', 'updated', 'skipped', 'notes' ) as $bucket) {
				if (empty($result[ $bucket ])) {
					continue;
				}
				foreach ($result[ $bucket ] as $line) {
					WP_CLI::log($line);
				}
			}
			WP_CLI::success('Enjoy Naturally starter content is in place.');
		}
	);
}

/**
 * Appearance → Starter content.
 */
function en_starter_menu() {
	add_theme_page(
		__('Starter content', 'enjoy-naturally'),
		__('Starter content', 'enjoy-naturally'),
		'manage_options',
		'enjoy-naturally-starter',
		'en_render_starter_page'
	);
}

/**
 * Run the import from the admin form.
 */
function en_starter_handle_post() {
	if (empty($_POST['en_import_starter'])) {
		return;
	}
	if (! current_user_can('manage_options')) {
		return;
	}
	check_admin_referer('en_import_starter');
	$result = en_import_starter(
		array(
			'replace'    => ! empty($_POST['en_replace']),
			'discourage' => ! empty($_POST['en_discourage']),
			'permalinks' => ! empty($_POST['en_permalinks']),
			'retitle'    => ! empty($_POST['en_retitle']),
		)
	);
	set_transient('en_import_result_' . get_current_user_id(), $result, MINUTE_IN_SECONDS);
	wp_safe_redirect(admin_url('themes.php?page=enjoy-naturally-starter&imported=1'));
	exit;
}

/**
 * Nudge an administrator to import on a fresh site.
 */
function en_starter_admin_notice() {
	if (! current_user_can('manage_options') || get_option('en_starter_imported')) {
		return;
	}
	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if ($screen && 'appearance_page_enjoy-naturally-starter' === $screen->id) {
		return;
	}
	$url = admin_url('themes.php?page=enjoy-naturally-starter');
	echo '<div class="notice notice-info"><p>';
	echo esc_html__('Enjoy Naturally is active. Import the starter stories, pages, and categories when you are ready.', 'enjoy-naturally');
	echo ' <a href="' . esc_url($url) . '">' . esc_html__('Open starter content', 'enjoy-naturally') . '</a>';
	echo '</p></div>';
}

/**
 * Admin screen.
 */
function en_render_starter_page() {
	if (! current_user_can('manage_options')) {
		return;
	}
	$result = get_transient('en_import_result_' . get_current_user_id());
	if (false !== $result) {
		delete_transient('en_import_result_' . get_current_user_id());
	}
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__('Starter content', 'enjoy-naturally') . '</h1>';
	echo '<p>' . esc_html__('This creates the Enjoy Naturally categories, the six publication pages, and the four launch stories with their photographs. Running it again skips anything that already uses the same slug unless you choose replace.', 'enjoy-naturally') . '</p>';

	if (is_wp_error($result)) {
		echo '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
	} elseif (is_array($result)) {
		echo '<div class="notice notice-success"><p>' . esc_html__('Import finished.', 'enjoy-naturally') . '</p></div>';
		foreach (array(
			'created' => __('Created', 'enjoy-naturally'),
			'updated' => __('Updated', 'enjoy-naturally'),
			'skipped' => __('Skipped', 'enjoy-naturally'),
			'notes'   => __('Notes', 'enjoy-naturally'),
		) as $key => $label) {
			if (empty($result[ $key ])) {
				continue;
			}
			echo '<h2>' . esc_html($label) . '</h2><ul>';
			foreach ($result[ $key ] as $line) {
				echo '<li>' . esc_html($line) . '</li>';
			}
			echo '</ul>';
		}
	}

	echo '<form method="post">';
	wp_nonce_field('en_import_starter');
	echo '<input type="hidden" name="en_import_starter" value="1">';
	echo '<p><label><input type="checkbox" name="en_retitle" value="1" checked> ' . esc_html__('Set the site title to Enjoy Naturally and the tagline to “Seasonal food, gardens, and family life.”', 'enjoy-naturally') . '</label></p>';
	echo '<p><label><input type="checkbox" name="en_permalinks" value="1" checked> ' . esc_html__('Use the Post name permalink structure (/%postname%/).', 'enjoy-naturally') . '</label></p>';
	echo '<p><label><input type="checkbox" name="en_discourage" value="1" checked> ' . esc_html__('Discourage search engines until launch (Settings → Reading).', 'enjoy-naturally') . '</label></p>';
	echo '<p><label><input type="checkbox" name="en_replace" value="1"> ' . esc_html__('Replace starter pages and posts that already exist. This overwrites their content.', 'enjoy-naturally') . '</label></p>';
	submit_button(__('Import starter content', 'enjoy-naturally'));
	echo '</form>';
	echo '<p class="description">' . esc_html__('New posts have comments turned off. The default Hello World post and Sample Page are moved to the trash when they are still untouched. Newsletter signups and contact notes are kept under Reader notes.', 'enjoy-naturally') . '</p>';
	echo '</div>';
}

/**
 * Import demo/content.json.
 *
 * @param array<string, bool> $args Flags: replace, discourage, permalinks, retitle.
 * @return array<string, array<int, string>>|WP_Error
 */
function en_import_starter($args = array()) {
	$args = wp_parse_args(
		$args,
		array(
			'replace'    => false,
			'discourage' => false,
			'permalinks' => true,
			'retitle'    => true,
		)
	);

	if (! get_current_user_id()) {
		$admin_id = en_primary_admin_id();
		if (! $admin_id) {
			return new WP_Error('en_no_admin', __('Starter import needs an administrator.', 'enjoy-naturally'));
		}
		wp_set_current_user($admin_id);
	}

	$data = en_starter_data();
	if (is_wp_error($data)) {
		return $data;
	}

	$result = array(
		'created' => array(),
		'updated' => array(),
		'skipped' => array(),
		'notes'   => array(),
	);

	if (! empty($args['retitle'])) {
		update_option('blogname', $data['site']['title']);
		update_option('blogdescription', $data['site']['tagline']);
		$result['notes'][] = __('Site title and tagline set.', 'enjoy-naturally');
	}
	if (! empty($args['permalinks']) && '/%postname%/' !== get_option('permalink_structure')) {
		update_option('permalink_structure', '/%postname%/');
		$result['notes'][] = __('Permalinks set to Post name.', 'enjoy-naturally');
	}
	if (! empty($args['discourage'])) {
		update_option('blog_public', '0');
		$result['notes'][] = __('Search engines are discouraged until you uncheck Settings → Reading.', 'enjoy-naturally');
	}
	update_option('default_comment_status', 'closed');
	update_option('default_ping_status', 'closed');

	$restore_kses = false;
	if (! current_user_can('unfiltered_html')) {
		kses_remove_filters();
		$restore_kses = true;
	}

	$category_ids = en_import_categories($data['categories'], ! empty($args['replace']), $result);
	$image_ids    = en_import_images($data['images'], $result);
	en_import_posts($data['posts'], $category_ids, $image_ids, ! empty($args['replace']), $result);
	en_import_pages($data['pages'], $image_ids, ! empty($args['replace']), $result);
	en_import_menus($data, $category_ids, ! empty($args['replace']), $result);
	en_trash_default_content($result);

	if ($restore_kses) {
		kses_init_filters();
	}

	en_register_category_rewrites();
	flush_rewrite_rules();
	update_option('en_starter_imported', gmdate('c'));
	$result['notes'][] = __('Rewrite rules flushed. Category URLs use the slug at the site root, such as /garden/.', 'enjoy-naturally');

	return $result;
}

/**
 * Load and lightly validate the JSON bundle.
 *
 * @return array<string, mixed>|WP_Error
 */
function en_starter_data() {
	$path = get_template_directory() . '/demo/content.json';
	if (! file_exists($path)) {
		return new WP_Error('en_missing_json', __('Starter file demo/content.json is missing from the theme.', 'enjoy-naturally'));
	}
	$raw = file_get_contents($path);
	if (! is_string($raw)) {
		return new WP_Error('en_bad_json', __('Starter file demo/content.json could not be read.', 'enjoy-naturally'));
	}
	$decoded = json_decode($raw, true);
	if (! is_array($decoded) || empty($decoded['posts']) || empty($decoded['pages']) || empty($decoded['categories'])) {
		return new WP_Error('en_bad_json', __('Starter file demo/content.json could not be read.', 'enjoy-naturally'));
	}
	return $decoded;
}

/**
 * Create categories.
 *
 * @param array<int, array<string, mixed>> $categories Category records.
 * @param bool                             $replace    Whether to refresh descriptions.
 * @param array<string, array<int, string>> $result    Report, by reference.
 * @return array<string, int> slug => term_id
 */
function en_import_categories($categories, $replace, &$result) {
	$ids = array();
	foreach ($categories as $category) {
		$existing = get_term_by('slug', $category['slug'], 'category');
		if ($existing instanceof WP_Term) {
			$ids[ $category['slug'] ] = (int) $existing->term_id;
			if ($replace || '' === trim(wp_strip_all_tags($existing->description))) {
				wp_update_term(
					$existing->term_id,
					'category',
					array(
						'name'        => $category['name'],
						'description' => $category['description'],
					)
				);
				$result['updated'][] = sprintf('Category %s', $category['name']);
			} else {
				$result['skipped'][] = sprintf('Category %s', $category['name']);
			}
			continue;
		}
		$created = wp_insert_term(
			$category['name'],
			'category',
			array(
				'slug'        => $category['slug'],
				'description' => $category['description'],
			)
		);
		if (is_wp_error($created)) {
			$result['notes'][] = $created->get_error_message();
			continue;
		}
		$ids[ $category['slug'] ] = (int) $created['term_id'];
		$result['created'][] = sprintf('Category %s', $category['name']);
	}
	return $ids;
}

/**
 * Copy theme photographs into the media library once.
 *
 * @param array<int, array<string, string>> $images Image records.
 * @param array<string, array<int, string>> $result Report.
 * @return array<string, int> filename => attachment ID
 */
function en_import_images($images, &$result) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$ids = array();
	foreach ($images as $image) {
		$file = $image['file'];
		$existing = en_find_theme_image($file);
		if ($existing) {
			$ids[ $file ] = $existing;
			if ('' === (string) get_post_meta($existing, '_wp_attachment_image_alt', true)) {
				update_post_meta($existing, '_wp_attachment_image_alt', $image['alt']);
			}
			continue;
		}
		$path = get_template_directory() . '/assets/images/' . $file;
		if (! file_exists($path)) {
			$result['notes'][] = sprintf('Missing theme image %s', $file);
			continue;
		}
		$tmp = wp_tempnam($file);
		if (! $tmp || ! copy($path, $tmp)) {
			$result['notes'][] = sprintf('Could not stage %s', $file);
			continue;
		}
		$attachment_id = media_handle_sideload(
			array(
				'name'     => $file,
				'tmp_name' => $tmp,
			),
			0,
			null,
			array(
				'post_title'   => preg_replace('/\.[^.]+$/', '', $file),
				'post_excerpt' => 'Photograph created for Enjoy Naturally.',
			)
		);
		if (is_wp_error($attachment_id)) {
			if (file_exists($tmp)) {
				wp_delete_file($tmp);
			}
			$result['notes'][] = $attachment_id->get_error_message();
			continue;
		}
		update_post_meta($attachment_id, '_wp_attachment_image_alt', $image['alt']);
		update_post_meta($attachment_id, '_en_theme_asset', $file);
		$ids[ $file ] = (int) $attachment_id;
		$result['created'][] = sprintf('Image %s', $file);
	}
	return $ids;
}

/**
 * Attachment already imported from a theme file.
 *
 * @param string $filename File name.
 * @return int
 */
function en_find_theme_image($filename) {
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_en_theme_asset',
			'meta_value'     => $filename,
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Create or update launch posts.
 *
 * @param array<int, array<string, mixed>>  $posts      Post records.
 * @param array<string, int>                $categories slug => term id.
 * @param array<string, int>                $images     filename => attachment id.
 * @param bool                              $replace    Overwrite existing slugs.
 * @param array<string, array<int, string>> $result     Report.
 */
function en_import_posts($posts, $categories, $images, $replace, &$result) {
	foreach ($posts as $post) {
		en_upsert_content('post', $post, $categories, $images, $replace, $result);
	}
}

/**
 * Create or update pages, and point the privacy policy setting at Privacy.
 *
 * @param array<int, array<string, mixed>>  $pages      Page records.
 * @param array<string, int>                $images     Image map.
 * @param bool                              $replace    Overwrite existing slugs.
 * @param array<string, array<int, string>> $result     Report.
 */
function en_import_pages($pages, $images, $replace, &$result) {
	foreach ($pages as $page) {
		$id = en_upsert_content('page', $page, array(), $images, $replace, $result);
		if ($id && ! empty($page['is_privacy_policy'])) {
			update_option('wp_page_for_privacy_policy', $id);
			$result['notes'][] = __('Privacy page set as the WordPress privacy policy.', 'enjoy-naturally');
		}
	}
}

/**
 * Insert or update one post or page.
 *
 * @param string                            $type       post|page.
 * @param array<string, mixed>              $record     JSON record.
 * @param array<string, int>                $categories Category map.
 * @param array<string, int>                $images     Image map.
 * @param bool                              $replace    Overwrite flag.
 * @param array<string, array<int, string>> $result     Report.
 * @return int Post ID, or 0.
 */
function en_upsert_content($type, $record, $categories, $images, $replace, &$result) {
	$existing = get_page_by_path($record['slug'], OBJECT, $type);
	$payload  = array(
		'post_type'     => $type,
		'post_status'   => 'publish',
		'post_title'    => $record['title'],
		'post_name'     => $record['slug'],
		'post_content'  => $record['content'],
		'post_excerpt'  => isset($record['excerpt']) ? $record['excerpt'] : '',
		'comment_status'=> 'closed',
		'ping_status'   => 'closed',
	);
	if (! empty($record['date'])) {
		$payload['post_date'] = $record['date'];
		$payload['edit_date'] = true;
	}
	if ('post' === $type && ! empty($record['category']) && ! empty($categories[ $record['category'] ])) {
		$payload['post_category'] = array( (int) $categories[ $record['category'] ] );
	}

	if ($existing instanceof WP_Post) {
		if (! $replace) {
			$result['skipped'][] = sprintf('%s “%s”', 'page' === $type ? 'Page' : 'Story', $record['title']);
			en_remember_starter_meta($existing->ID, $record, $images, false);
			return (int) $existing->ID;
		}
		$payload['ID'] = $existing->ID;
		$updated       = wp_update_post(wp_slash($payload), true);
		if (is_wp_error($updated)) {
			$result['notes'][] = $updated->get_error_message();
			return 0;
		}
		en_remember_starter_meta((int) $updated, $record, $images, true);
		$result['updated'][] = sprintf('%s “%s”', 'page' === $type ? 'Page' : 'Story', $record['title']);
		return (int) $updated;
	}

	$created = wp_insert_post(wp_slash($payload), true);
	if (is_wp_error($created)) {
		$result['notes'][] = $created->get_error_message();
		return 0;
	}
	en_remember_starter_meta((int) $created, $record, $images, true);
	if (! empty($record['tag'])) {
		wp_set_post_tags((int) $created, array( $record['tag'] ), false);
	}
	$result['created'][] = sprintf('%s “%s”', 'page' === $type ? 'Page' : 'Story', $record['title']);
	return (int) $created;
}

/**
 * Store kicker, description, minutes, and the featured image.
 *
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $record  JSON record.
 * @param array<string, int>   $images  Image map.
 * @param bool                 $force   Replace an existing featured image.
 */
function en_remember_starter_meta($post_id, $record, $images, $force) {
	if (! empty($record['kicker'])) {
		update_post_meta($post_id, '_en_kicker', $record['kicker']);
	}
	if (! empty($record['description'])) {
		update_post_meta($post_id, '_en_description', $record['description']);
	}
	if (! empty($record['minutes'])) {
		update_post_meta($post_id, '_en_minutes', (int) $record['minutes']);
	}
	update_post_meta($post_id, '_en_starter', '1');
	if (! empty($record['image']) && ! empty($images[ $record['image'] ])) {
		if ($force || ! has_post_thumbnail($post_id)) {
			set_post_thumbnail($post_id, (int) $images[ $record['image'] ]);
		}
	}
}

/**
 * Build the three menus when they are empty, or rebuild them on replace.
 *
 * @param array<string, mixed>              $data       Full JSON.
 * @param array<string, int>                $categories Category map.
 * @param bool                              $replace    Rebuild menus.
 * @param array<string, array<int, string>> $result     Report.
 */
function en_import_menus($data, $categories, $replace, &$result) {
	unset($data);
	$primary = en_ensure_menu(__('Primary', 'enjoy-naturally'), $replace, $result);
	$explore = en_ensure_menu(__('Explore', 'enjoy-naturally'), $replace, $result);
	$pages   = en_ensure_menu(__('Publication', 'enjoy-naturally'), $replace, $result);

	if ($primary && en_menu_needs_items($primary, $replace)) {
		en_clear_menu($primary);
		$position = 1;
		en_add_menu_item(
			$primary,
			array(
				'menu-item-title'    => __('Home', 'enjoy-naturally'),
				'menu-item-url'      => home_url('/'),
				'menu-item-status'   => 'publish',
				'menu-item-type'     => 'custom',
				'menu-item-position' => $position++,
			)
		);
		foreach (en_primary_links() as $item) {
			if ('category' !== $item['kind'] || empty($categories[ $item['slug'] ])) {
				continue;
			}
			en_add_menu_item(
				$primary,
				array(
					'menu-item-title'     => $item['label'],
					'menu-item-object'    => 'category',
					'menu-item-object-id' => $categories[ $item['slug'] ],
					'menu-item-type'      => 'taxonomy',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position++,
				)
			);
		}
		$about = get_page_by_path('about');
		if ($about instanceof WP_Post) {
			en_add_menu_item(
				$primary,
				array(
					'menu-item-title'     => __('About', 'enjoy-naturally'),
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $about->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position,
				)
			);
		}
		$result['created'][] = __('Primary menu', 'enjoy-naturally');
	}

	if ($explore && en_menu_needs_items($explore, $replace)) {
		en_clear_menu($explore);
		$position = 1;
		foreach (en_explore_links() as $slug => $label) {
			if (empty($categories[ $slug ])) {
				continue;
			}
			en_add_menu_item(
				$explore,
				array(
					'menu-item-title'     => $label,
					'menu-item-object'    => 'category',
					'menu-item-object-id' => $categories[ $slug ],
					'menu-item-type'      => 'taxonomy',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position++,
				)
			);
		}
		$result['created'][] = __('Explore menu', 'enjoy-naturally');
	}

	if ($pages && en_menu_needs_items($pages, $replace)) {
		en_clear_menu($pages);
		$position = 1;
		foreach (en_publication_links() as $slug => $label) {
			$page = get_page_by_path($slug);
			if (! $page instanceof WP_Post) {
				continue;
			}
			en_add_menu_item(
				$pages,
				array(
					'menu-item-title'     => $label,
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $page->ID,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position++,
				)
			);
		}
		$result['created'][] = __('Publication menu', 'enjoy-naturally');
	}

	$locations = get_theme_mod('nav_menu_locations', array());
	if (! is_array($locations)) {
		$locations = array();
	}
	if ($primary) {
		$locations['primary'] = $primary;
	}
	if ($explore) {
		$locations['footer_explore'] = $explore;
	}
	if ($pages) {
		$locations['footer_publication'] = $pages;
	}
	set_theme_mod('nav_menu_locations', $locations);
}

/**
 * Find or create a menu.
 *
 * @param string                            $name    Menu name.
 * @param bool                              $replace Unused. Kept for call symmetry.
 * @param array<string, array<int, string>> $result  Report.
 * @return int
 */
function en_ensure_menu($name, $replace, &$result) {
	unset($replace);
	$menu = wp_get_nav_menu_object($name);
	if ($menu) {
		return (int) $menu->term_id;
	}
	$created = wp_create_nav_menu($name);
	if (is_wp_error($created)) {
		$result['notes'][] = $created->get_error_message();
		return 0;
	}
	return (int) $created;
}

/**
 * True when the menu should be (re)filled.
 *
 * @param int  $menu_id Menu ID.
 * @param bool $replace Replace flag.
 * @return bool
 */
function en_menu_needs_items($menu_id, $replace) {
	$items = wp_get_nav_menu_items($menu_id);
	$has   = is_array($items) && count($items) > 0;
	return $replace || ! $has;
}

/**
 * Delete every item in a menu.
 *
 * @param int $menu_id Menu ID.
 */
function en_clear_menu($menu_id) {
	$items = wp_get_nav_menu_items($menu_id);
	if (! is_array($items)) {
		return;
	}
	foreach ($items as $item) {
		wp_delete_post($item->ID, true);
	}
}

/**
 * Add one menu item and ignore a failed insert in the report by returning 0.
 *
 * @param int                  $menu_id Menu ID.
 * @param array<string, mixed> $args    Menu item args.
 * @return int
 */
function en_add_menu_item($menu_id, $args) {
	$result = wp_update_nav_menu_item($menu_id, 0, $args);
	return is_wp_error($result) ? 0 : (int) $result;
}

/**
 * Trash the untouched default post and page.
 *
 * @param array<string, array<int, string>> $result Report.
 */
function en_trash_default_content(&$result) {
	$hello = get_page_by_path('hello-world', OBJECT, 'post');
	if ($hello instanceof WP_Post && 'Hello world!' === $hello->post_title && 'publish' === $hello->post_status) {
		wp_trash_post($hello->ID);
		$result['notes'][] = __('Moved the default “Hello world!” post to the trash.', 'enjoy-naturally');
	}
	$sample = get_page_by_path('sample-page');
	if ($sample instanceof WP_Post && 'Sample Page' === $sample->post_title && 'publish' === $sample->post_status) {
		wp_trash_post($sample->ID);
		$result['notes'][] = __('Moved the default Sample Page to the trash.', 'enjoy-naturally');
	}
	$stock_privacy = get_page_by_path('privacy-policy');
	if ($stock_privacy instanceof WP_Post && 'Privacy Policy' === $stock_privacy->post_title && 'draft' === $stock_privacy->post_status) {
		wp_trash_post($stock_privacy->ID);
		$result['notes'][] = __('Moved the unused draft Privacy Policy page to the trash.', 'enjoy-naturally');
	}
}
