<?php
/**
 * Small template helpers.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

/**
 * Theme asset URL.
 *
 * @param string $path Path under assets/.
 * @return string
 */
function en_asset($path) {
	return get_template_directory_uri() . '/assets/' . ltrim($path, '/');
}

/**
 * Build an attribute string from trusted keys.
 *
 * @param array<string, mixed> $attrs Attributes.
 * @return string
 */
function en_html_attrs($attrs) {
	$html = '';
	foreach ($attrs as $key => $value) {
		if (null === $value || false === $value || '' === $value) {
			continue;
		}
		$html .= ' ' . esc_attr($key) . '="' . esc_attr((string) $value) . '"';
	}
	return $html;
}

/**
 * Image tag for a theme photograph.
 *
 * @param string               $file  File name in assets/images.
 * @param string               $alt   Alt text.
 * @param int                  $width Width.
 * @param int                  $height Height.
 * @param array<string, mixed> $attrs Extra attributes.
 * @return string
 */
function en_img($file, $alt, $width, $height, $attrs = array()) {
	$attrs = array_merge(
		array(
			'src'      => en_asset('images/' . $file),
			'alt'      => $alt,
			'width'    => (int) $width,
			'height'   => (int) $height,
			'loading'  => 'lazy',
			'decoding' => 'async',
		),
		$attrs
	);

	return '<img' . en_html_attrs($attrs) . '>';
}

/**
 * Published post for a catalog slug, cached for the request.
 *
 * @param string $slug Post slug.
 * @return WP_Post|null
 */
function en_post_by_slug($slug) {
	static $cache = array();
	if (array_key_exists($slug, $cache)) {
		return $cache[ $slug ];
	}
	$post = get_page_by_path($slug, OBJECT, 'post');
	if (! $post instanceof WP_Post || 'publish' !== $post->post_status) {
		$post = null;
	}
	$cache[ $slug ] = $post;
	return $post;
}

/**
 * Longer essay for a category archive, shipped with the theme.
 *
 * @param string $slug Category slug.
 * @return string
 */
/**
 * Extra sections for a page or post, skipped when starter content already includes them.
 *
 * @param string $slug Post slug.
 * @return string
 */
function en_supplemental_copy($slug) {
	$post = get_post();
	if ($post instanceof WP_Post && false !== strpos($post->post_content, 'en-extra')) {
		return '';
	}
	$slug = sanitize_title($slug);
	if (! preg_match('/^[a-z0-9-]+$/', $slug)) {
		return '';
	}
	$path = get_template_directory() . '/demo/extras/' . $slug . '.html';
	if (! is_readable($path)) {
		return '';
	}
	$html = file_get_contents($path);
	if (! is_string($html) || '' === trim($html)) {
		return '';
	}
	return wp_kses_post(en_expand_root_urls($html));
}

function en_category_hub($slug) {
	$slug = sanitize_title($slug);
	if (! preg_match('/^[a-z0-9-]+$/', $slug)) {
		return '';
	}
	$path = get_template_directory() . '/demo/hubs/' . $slug . '.html';
	if (! is_readable($path)) {
		return '';
	}
	$html = file_get_contents($path);
	if (! is_string($html) || '' === trim($html)) {
		return '';
	}
	return en_expand_root_urls($html);
}

function en_category_url($slug) {
	$term = get_term_by('slug', $slug, 'category');
	if ($term instanceof WP_Term) {
		$link = get_term_link($term);
		if (! is_wp_error($link)) {
			return $link;
		}
	}
	return home_url(user_trailingslashit($slug));
}

/**
 * Page URL, falling back to the expected slug.
 *
 * @param string $slug Page slug.
 * @return string
 */
function en_page_url($slug) {
	$page = get_page_by_path($slug);
	if ($page instanceof WP_Post) {
		return get_permalink($page);
	}
	return home_url(user_trailingslashit($slug));
}

/**
 * Resolve a primary-nav item to a URL.
 *
 * @param array<string, string> $item Nav item.
 * @return string
 */
function en_nav_url($item) {
	if ('home' === $item['kind']) {
		return home_url('/');
	}
	if ('category' === $item['kind']) {
		return en_category_url($item['slug']);
	}
	if ('page' === $item['kind']) {
		return en_page_url($item['slug']);
	}
	return home_url('/');
}

/**
 * Whether a fallback nav item is the current view.
 *
 * @param array<string, string> $item Nav item.
 * @return bool
 */
function en_nav_is_current($item) {
	if ('home' === $item['kind']) {
		return is_front_page();
	}
	if ('category' === $item['kind']) {
		return is_category($item['slug']);
	}
	if ('page' === $item['kind']) {
		return is_page($item['slug']);
	}
	return false;
}

/**
 * Eyebrow for a post: starter meta, then the catalog, then the category.
 *
 * @param int $post_id Post ID. Defaults to the current post.
 * @return string
 */
function en_kicker($post_id = 0) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$meta    = get_post_meta($post_id, '_en_kicker', true);
	if (is_string($meta) && '' !== $meta) {
		return $meta;
	}

	$post = get_post($post_id);
	if ($post instanceof WP_Post) {
		$item = en_catalog_item($post->post_name);
		if ($item) {
			return $item['kicker'];
		}
		if ('page' === $post->post_type) {
			return get_the_title($post);
		}
	}

	$cats = get_the_category($post_id);
	if ($cats) {
		return $cats[0]->name;
	}

	return __('Story', 'enjoy-naturally');
}

/**
 * Minutes to read. Starter posts store the preview's count.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function en_reading_minutes($post_id = 0) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$stored  = (int) get_post_meta($post_id, '_en_minutes', true);
	if ($stored > 0) {
		return $stored;
	}
	$post  = get_post($post_id);
	$words = $post instanceof WP_Post ? str_word_count(wp_strip_all_tags($post->post_content)) : 0;
	return max(1, (int) round($words / 220));
}

/**
 * Meta description for the current view.
 *
 * @return string
 */
function en_meta_description() {
	if (is_singular()) {
		$post_id = get_queried_object_id();
		$custom  = get_post_meta($post_id, '_en_description', true);
		if (is_string($custom) && '' !== $custom) {
			return $custom;
		}
		if (has_excerpt($post_id)) {
			return wp_strip_all_tags(get_the_excerpt($post_id));
		}
		$post = get_post($post_id);
		return $post instanceof WP_Post ? wp_trim_words(wp_strip_all_tags($post->post_content), 28, '…') : '';
	}
	if (is_category() || is_tag() || is_tax()) {
		$term = get_queried_object();
		if ($term instanceof WP_Term && '' !== $term->description) {
			return wp_strip_all_tags($term->description);
		}
	}
	if (is_front_page()) {
		return __('Recipes, yoga, and everyday ideas from Enjoy Naturally, a personal blog.', 'enjoy-naturally');
	}
	return get_bloginfo('description');
}

/**
 * Share image for the current view.
 *
 * @return string
 */
function en_share_image() {
	if (is_singular() && has_post_thumbnail()) {
		$src = wp_get_attachment_image_url(get_post_thumbnail_id(), 'full');
		if ($src) {
			return $src;
		}
	}
	return en_asset('images/hero-november.jpg');
}

/**
 * Rewrite root-relative links so a subdirectory install still resolves.
 *
 * @param string $html HTML.
 * @return string
 */
function en_expand_root_urls($html) {
	if (! is_string($html) || '' === $html || false === strpos($html, '="/') && false === strpos($html, "='/")) {
		return $html;
	}
	$rewritten = preg_replace_callback(
		'#\b(href|src)=([\'"])/(?!/)([^\'"]*)\2#i',
		static function ($matches) {
			$url = home_url('/' . ltrim($matches[3], '/'));
			return $matches[1] . '=' . $matches[2] . esc_url($url) . $matches[2];
		},
		$html
	);
	return is_string($rewritten) ? $rewritten : $html;
}

/**
 * Breadcrumb trail. The last item is the current page.
 *
 * @param array<int, array<string, string>> $items Items with label and optional url.
 */
function en_breadcrumbs($items) {
	if (! $items) {
		return;
	}
	echo '<nav class="crumbs" aria-label="' . esc_attr__('Breadcrumb', 'enjoy-naturally') . '"><ol>';
	$last = count($items) - 1;
	foreach ($items as $index => $item) {
		$label = isset($item['label']) ? $item['label'] : '';
		if ($index === $last) {
			echo '<li aria-current="page">' . esc_html($label) . '</li>';
			continue;
		}
		echo '<li><a href="' . esc_url($item['url']) . '">' . esc_html($label) . '</a></li>';
	}
	echo '</ol></nav>';
}

/**
 * Related published stories.
 *
 * @param int $post_id Current post.
 * @param int $limit   How many.
 * @return WP_Post[]
 */
function en_related_posts($post_id, $limit = 2) {
	$cats = wp_get_post_categories($post_id);
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $limit,
		'post__not_in'        => array( (int) $post_id ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ($cats) {
		$args['category__in'] = $cats;
	}
	$query = new WP_Query($args);
	$posts = $query->posts;
	if (count($posts) < $limit) {
		$ids  = array_merge(array( (int) $post_id ), wp_list_pluck($posts, 'ID'));
		$more = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'numberposts'         => $limit - count($posts),
				'post__not_in'        => $ids,
				'ignore_sticky_posts' => true,
			)
		);
		$posts = array_merge($posts, $more);
	}
	return $posts;
}

/**
 * Footer journal entries, launch order.
 *
 * @return array<int, array<string, string>>
 */
function en_journal_entries() {
	$entries = array();
	foreach (en_journal_slugs() as $slug) {
		$post = en_post_by_slug($slug);
		$item = en_catalog_item($slug);
		$entries[] = array(
			'title' => $post instanceof WP_Post ? get_the_title($post) : ( $item ? $item['title'] : $slug ),
			'url'   => $post instanceof WP_Post ? get_permalink($post) : home_url(user_trailingslashit($slug)),
		);
	}
	return $entries;
}

/**
 * Calendar previews for a category that are not published yet.
 *
 * @param string $slug Category slug.
 * @return array<string, array<string, mixed>>
 */
function en_calendar_for_category($slug) {
	$notes = array();
	foreach (en_catalog() as $key => $item) {
		if ($item['category'] !== $slug || 'preview' !== $item['kind']) {
			continue;
		}
		if (en_post_by_slug($key)) {
			continue;
		}
		$notes[ $key ] = $item;
	}
	return $notes;
}

/**
 * Client-side search records: stories, pages, categories, and calendar notes.
 *
 * @return array<int, array<string, string>>
 */
function en_search_index_items() {
	$items = array();

	$posts = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => 100,
		)
	);
	foreach ($posts as $post) {
		$catalog = en_catalog_item($post->post_name);
		$kind    = ( $catalog && 'recipe' === $catalog['kind'] ) ? 'recipe' : 'article';
		$text    = has_excerpt($post) ? $post->post_excerpt : wp_trim_words(wp_strip_all_tags($post->post_content), 28, '…');
		$items[] = array(
			'title'     => get_the_title($post),
			'href'      => get_permalink($post),
			'text'      => wp_strip_all_tags($text),
			'kind'      => $kind,
			'kindLabel' => 'recipe' === $kind ? __('Recipe', 'enjoy-naturally') : __('Story', 'enjoy-naturally'),
		);
	}

	$pages = get_pages(
		array(
			'sort_column' => 'menu_order,post_title',
			'post_status' => 'publish',
		)
	);
	foreach ($pages as $page) {
		$items[] = array(
			'title'     => get_the_title($page),
			'href'      => get_permalink($page),
			'text'      => has_excerpt($page) ? wp_strip_all_tags($page->post_excerpt) : '',
			'kind'      => 'page',
			'kindLabel' => __('Page', 'enjoy-naturally'),
		);
	}

	$categories = get_categories(array( 'hide_empty' => false ));
	foreach ($categories as $category) {
		if ('uncategorized' === $category->slug) {
			continue;
		}
		$link = get_term_link($category);
		if (is_wp_error($link)) {
			continue;
		}
		$items[] = array(
			'title'     => $category->name,
			'href'      => $link,
			'text'      => wp_strip_all_tags($category->description),
			'kind'      => 'category',
			'kindLabel' => __('Category', 'enjoy-naturally'),
		);
	}

	foreach (en_catalog() as $slug => $item) {
		if ('preview' !== $item['kind'] || en_post_by_slug($slug)) {
			continue;
		}
		$items[] = array(
			'title'     => $item['title'],
			'href'      => en_category_url($item['category']) . '#' . $slug,
			'text'      => $item['excerpt'],
			'kind'      => 'preview',
			'kindLabel' => __('On the calendar', 'enjoy-naturally'),
		);
	}

	return $items;
}

/**
 * Print the search index used by assets/js/main.js.
 */
function en_print_search_index() {
	$json = wp_json_encode(en_search_index_items(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	if (! is_string($json)) {
		$json = '[]';
	}
	echo '<script id="search-index" type="application/json">' . $json . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode hex-escapes tags.
}

/**
 * An SEO plugin will print its own social tags.
 *
 * @return bool
 */
function en_seo_plugin_active() {
	return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION');
}
