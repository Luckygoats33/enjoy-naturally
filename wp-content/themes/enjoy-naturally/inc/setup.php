<?php
/**
 * Theme supports, assets, rewrites, and document head.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

add_action('after_setup_theme', 'en_setup');
add_action('wp_enqueue_scripts', 'en_enqueue_assets');
add_action('wp_head', 'en_head_tags', 2);
add_action('init', 'en_register_category_rewrites', 20);
add_filter('category_link', 'en_category_link', 10, 2);
add_filter('redirect_canonical', 'en_stop_category_base_redirect', 10, 2);
add_action('template_redirect', 'en_redirect_category_base');
add_action('after_switch_theme', 'en_on_switch');
add_action('created_category', 'en_schedule_rewrite_flush');
add_action('edited_category', 'en_schedule_rewrite_flush');
add_action('delete_category', 'en_schedule_rewrite_flush');
add_filter('nav_menu_link_attributes', 'en_nav_aria_current', 10, 2);
add_filter('nav_menu_css_class', 'en_nav_home_class', 10, 2);
add_filter('document_title_separator', 'en_title_separator');
add_filter('document_title_parts', 'en_document_title_parts');
add_filter('get_the_archive_title', 'en_archive_title');
add_filter('the_content', 'en_expand_root_urls', 20);
add_filter('excerpt_more', 'en_excerpt_more');

/**
 * Register theme features.
 */
function en_setup() {
	load_theme_textdomain('enjoy-naturally', get_template_directory() . '/languages');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
	add_theme_support('responsive-embeds');
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support('editor-styles');
	add_editor_style(
		array(
			'assets/css/styles.css',
			'assets/css/editor.css',
		)
	);

	register_nav_menus(
		array(
			'primary'            => __('Primary', 'enjoy-naturally'),
			'footer_explore'     => __('Footer explore', 'enjoy-naturally'),
			'footer_publication' => __('Footer publication', 'enjoy-naturally'),
		)
	);

	add_image_size('en-card', 800, 800, true);
	add_image_size('en-story', 420, 330, true);

	global $content_width;
	if (! isset($content_width)) {
		$content_width = 736;
	}
}

/**
 * Front-end styles and the small interaction script.
 */
function en_enqueue_assets() {
	$base = get_template_directory() . '/assets/css/styles.css';
	$wp   = get_template_directory() . '/assets/css/wp.css';
	$js   = get_template_directory() . '/assets/js/main.js';

	wp_enqueue_style('enjoy-naturally-base', get_template_directory_uri() . '/assets/css/styles.css', array(), file_exists($base) ? (string) filemtime($base) : EN_VERSION);
	wp_enqueue_style('enjoy-naturally-wp', get_template_directory_uri() . '/assets/css/wp.css', array( 'enjoy-naturally-base' ), file_exists($wp) ? (string) filemtime($wp) : EN_VERSION);
	wp_enqueue_style('enjoy-naturally-style', get_stylesheet_uri(), array( 'enjoy-naturally-wp' ), wp_get_theme()->get('Version'));
	wp_enqueue_script('enjoy-naturally', get_template_directory_uri() . '/assets/js/main.js', array(), file_exists($js) ? (string) filemtime($js) : EN_VERSION, true);
}

/**
 * Favicon, font preload, description, and basic social tags.
 */
function en_head_tags() {
	echo '<link rel="preload" href="' . esc_url(en_asset('fonts/fraunces-latin.woff2')) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<meta name="theme-color" content="#f6f2ea">' . "\n";

	if (! has_site_icon()) {
		echo '<link rel="icon" href="' . esc_url(en_asset('logo-mark.svg')) . '" type="image/svg+xml">' . "\n";
	}

	if (en_seo_plugin_active()) {
		return;
	}

	$description = en_meta_description();
	if ('' !== $description) {
		echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
	}

	$title = wp_get_document_title();
	$image = en_share_image();
	$url   = en_current_share_url();
	$type  = is_singular('post') ? 'article' : 'website';

	echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	en_print_json_ld();
}

/**
 * Canonical-ish URL for social tags.
 *
 * @return string
 */
function en_current_share_url() {
	if (is_singular()) {
		$canonical = wp_get_canonical_url();
		if ($canonical) {
			return $canonical;
		}
	}
	if (is_front_page()) {
		return home_url('/');
	}
	if (is_category() || is_tag() || is_tax()) {
		$term = get_queried_object();
		if ($term instanceof WP_Term) {
			$link = get_term_link($term);
			if (! is_wp_error($link)) {
				return $link;
			}
		}
	}
	return home_url('/');
}

/**
 * Minimal JSON-LD. Skipped when an SEO plugin is active.
 */
function en_print_json_ld() {
	if (is_front_page()) {
		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'WebSite',
			'name'        => get_bloginfo('name'),
			'url'         => home_url('/'),
			'description' => en_meta_description(),
			'publisher'   => array(
				'@type' => 'Organization',
				'name'  => "Nature's Turn",
			),
		);
	} elseif (is_singular('post')) {
		$post_id = get_queried_object_id();
		$data    = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => get_the_title($post_id),
			'datePublished'    => get_the_date('c', $post_id),
			'dateModified'     => get_the_modified_date('c', $post_id),
			'description'      => en_meta_description(),
			'mainEntityOfPage' => get_permalink($post_id),
			'image'            => en_share_image(),
			'author'           => array(
				'@type' => 'Organization',
				'name'  => 'Enjoy Naturally',
			),
			'publisher'        => array(
				'@type' => 'Organization',
				'name'  => "Nature's Turn",
			),
		);
	} else {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

/**
 * Serve categories at /slug/ so the journal matches the static URLs.
 */
function en_register_category_rewrites() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
		)
	);
	if (is_wp_error($terms) || ! is_array($terms)) {
		return;
	}
	foreach ($terms as $term) {
		if ('uncategorized' === $term->slug) {
			continue;
		}
		$slug = preg_quote($term->slug, '#');
		$name = $term->slug;
		add_rewrite_rule('^' . $slug . '/?$', 'index.php?category_name=' . $name, 'top');
		add_rewrite_rule('^' . $slug . '/page/([0-9]{1,})/?$', 'index.php?category_name=' . $name . '&paged=$matches[1]', 'top');
		add_rewrite_rule('^' . $slug . '/feed/?$', 'index.php?category_name=' . $name . '&feed=rss2', 'top');
		add_rewrite_rule('^' . $slug . '/(feed|rdf|rss|rss2|atom)/?$', 'index.php?category_name=' . $name . '&feed=$matches[1]', 'top');
	}
}

/**
 * Category permalinks omit /category/.
 *
 * @param string $termlink Default link.
 * @param int    $term_id  Term ID.
 * @return string
 */
function en_category_link($termlink, $term_id) {
	$term = get_term((int) $term_id, 'category');
	if (! $term instanceof WP_Term || is_wp_error($term) || 'uncategorized' === $term->slug) {
		return $termlink;
	}
	return home_url(user_trailingslashit($term->slug));
}

/**
 * Keep WordPress from sending /garden/ back to /category/garden/.
 *
 * @param string|false $redirect_url  Proposed redirect.
 * @param string       $requested_url Requested URL.
 * @return string|false
 */
function en_stop_category_base_redirect($redirect_url, $requested_url) {
	unset($requested_url);
	if (is_category() && is_string($redirect_url) && false !== strpos($redirect_url, '/category/')) {
		return false;
	}
	return $redirect_url;
}

/**
 * Send the old /category/slug/ form to the root slug.
 */
function en_redirect_category_base() {
	if (! is_category()) {
		return;
	}
	$request = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
	$path    = wp_parse_url($request, PHP_URL_PATH);
	if (! is_string($path) || ! preg_match('#/category/([^/]+)/?$#', $path, $matches)) {
		return;
	}
	wp_safe_redirect(home_url(user_trailingslashit(rawurldecode($matches[1]))), 301);
	exit;
}

/**
 * Plain permalinks become Post name, then rules are flushed.
 */
function en_on_switch() {
	if ('' === get_option('permalink_structure')) {
		update_option('permalink_structure', '/%postname%/');
	}
	en_register_category_rewrites();
	flush_rewrite_rules();
}

/**
 * Flush once after a category is created, renamed, or deleted.
 */
function en_schedule_rewrite_flush() {
	static $scheduled = false;
	if ($scheduled) {
		return;
	}
	$scheduled = true;
	add_action(
		'shutdown',
		static function () {
			en_register_category_rewrites();
			flush_rewrite_rules(false);
		}
	);
}

/**
 * Mark the current menu link for the existing CSS.
 *
 * @param array<string, string> $atts Link attributes.
 * @param WP_Post               $item Menu item.
 * @return array<string, string>
 */
function en_nav_aria_current($atts, $item) {
	$classes = is_array($item->classes) ? $item->classes : array();
	if (in_array('current-menu-item', $classes, true) || in_array('current_page_item', $classes, true)) {
		$atts['aria-current'] = 'page';
	}
	return $atts;
}

/**
 * Highlight the home custom link on the front page.
 *
 * @param string[] $classes Classes.
 * @param WP_Post  $item    Menu item.
 * @return string[]
 */
function en_nav_home_class($classes, $item) {
	if (! is_front_page() || empty($item->url)) {
		return $classes;
	}
	$home = untrailingslashit(home_url());
	$url  = untrailingslashit($item->url);
	if ($url === $home) {
		$classes[] = 'current-menu-item';
	}
	return $classes;
}

/**
 * Use the preview's title separator.
 *
 * @return string
 */
function en_title_separator() {
	return '|';
}

/**
 * Front page title uses the seasonal headline.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function en_document_title_parts($parts) {
	if (is_front_page()) {
		$parts['title']   = get_bloginfo('name');
		$parts['tagline'] = wp_strip_all_tags(en_mod('en_hero_title'));
	}
	return $parts;
}

/**
 * Archive titles without the "Category:" prefix.
 *
 * @param string $title Default title.
 * @return string
 */
function en_archive_title($title) {
	if (is_category()) {
		return single_cat_title('', false);
	}
	if (is_tag()) {
		return single_tag_title('', false);
	}
	if (is_author()) {
		return get_the_author();
	}
	return $title;
}

/**
 * A quieter excerpt ellipsis.
 *
 * @return string
 */
function en_excerpt_more() {
	return '…';
}
