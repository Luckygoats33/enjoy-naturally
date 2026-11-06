<?php
/**
 * Markup for the homepage bands, cards, and forms.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

/**
 * One homepage band.
 *
 * @param array<string, mixed> $band Band definition.
 */
function en_render_band($band) {
	$title_id = $band['id'] . '-title';
	$photo    = en_img($band['photo']['file'], $band['photo']['alt'], $band['photo']['width'], $band['photo']['height']);
	$figure   = '<figure class="band-photo">' . $photo . '</figure>';
	$intro    = '<div class="band-intro"><p class="eyebrow">' . en_icon('leaf') . esc_html($band['eyebrow']) . '</p>';
	$intro   .= '<h2 id="' . esc_attr($title_id) . '">' . esc_html($band['title']) . '</h2>';
	$intro   .= '<p class="dek">' . esc_html($band['dek']) . '</p></div>';

	ob_start();
	echo '<div class="' . esc_attr($band['cards_class']) . '">';
	foreach ($band['cards'] as $slug) {
		en_render_card_slug($slug);
	}
	echo '</div>';
	$cards = ob_get_clean();

	echo '<section class="' . esc_attr($band['class']) . '" aria-labelledby="' . esc_attr($title_id) . '">';
	if (! empty($band['photo_first'])) {
		echo $figure . $intro . $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from escaped pieces and theme icons.
	} else {
		echo $intro . $cards . $figure; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</section>';
}

/**
 * Card for a catalog slug. Published posts win over previews.
 *
 * @param string $slug Catalog slug.
 */
function en_render_card_slug($slug) {
	$post = en_post_by_slug($slug);
	if ($post instanceof WP_Post) {
		en_render_card_post($post, false);
		return;
	}
	$item = en_catalog_item($slug);
	if (! $item) {
		return;
	}
	$url  = en_category_url($item['category']) . '#' . rawurlencode($slug);
	$meta = $item['kicker'] . ' · ' . __('Preview', 'enjoy-naturally');
	en_render_card(
		array(
			'url'   => $url,
			'title' => $item['title'],
			'meta'  => $meta,
			'media' => en_img($item['image'], $item['alt'], $item['width'], $item['height']),
		)
	);
}

/**
 * Card for any published post.
 *
 * @param WP_Post $post    Post object.
 * @param bool    $preview Unused. Published cards never say Preview.
 */
function en_render_card_post($post, $preview = false) {
	unset($preview);
	$item  = en_catalog_item($post->post_name);
	$media = '';
	$thumb = get_post_thumbnail_id($post);
	if ($thumb) {
		$alt = get_post_meta($thumb, '_wp_attachment_image_alt', true);
		if (! is_string($alt) || '' === $alt) {
			$alt = $item ? $item['alt'] : get_the_title($post);
		}
		$media = wp_get_attachment_image(
			$thumb,
			'en-card',
			false,
			array(
				'loading' => 'lazy',
				'alt'     => $alt,
			)
		);
	} elseif ($item) {
		$media = en_img($item['image'], $item['alt'], $item['width'], $item['height']);
	}

	en_render_card(
		array(
			'url'   => get_permalink($post),
			'title' => get_the_title($post),
			'meta'  => en_kicker($post->ID),
			'media' => $media,
		)
	);
}

/**
 * Shared card markup.
 *
 * @param array<string, string> $card url, title, meta, media.
 */
function en_render_card($card) {
	echo '<a class="card" href="' . esc_url($card['url']) . '">';
	echo '<div class="card-media">' . $card['media'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image HTML from wp_get_attachment_image or en_img.
	echo '<h3>' . esc_html($card['title']) . '</h3>';
	echo '<p class="card-meta">' . esc_html($card['meta']) . '</p>';
	echo '</a>';
}

/**
 * Recipe-of-the-week panel.
 */
function en_render_recipe_panel() {
	$slug = 'creamy-wild-rice-mushroom-soup';
	$item = en_catalog_item($slug);
	$post = en_post_by_slug($slug);
	$title = $post instanceof WP_Post ? get_the_title($post) : ( $item ? $item['title'] : '' );
	$text  = $item ? $item['excerpt'] : '';
	$url   = $post instanceof WP_Post ? get_permalink($post) : home_url(user_trailingslashit($slug));
	$media = '';
	if ($post instanceof WP_Post && get_post_thumbnail_id($post)) {
		$media = wp_get_attachment_image(get_post_thumbnail_id($post), 'large', false, array( 'loading' => 'lazy' ));
	} elseif ($item) {
		$media = en_img($item['image'], $item['alt'], $item['width'], $item['height']);
	}

	echo '<article class="panel recipe-panel"><div>';
	echo '<p class="eyebrow">' . en_icon('leaf') . esc_html__('Recipe of the week', 'enjoy-naturally') . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<h2>' . esc_html($title) . '</h2>';
	echo '<p>' . esc_html($text) . '</p>';
	echo '<a class="link-arrow" href="' . esc_url($url) . '">' . esc_html__('View recipe', 'enjoy-naturally') . ' ' . en_icon('arrow') . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div><figure class="recipe-photo">' . $media . '</figure></article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Homepage newsletter panel.
 */
function en_render_newsletter_panel() {
	echo '<aside class="panel newsletter-panel" aria-labelledby="newsletter-title">';
	echo '<h2 id="newsletter-title">' . en_icon('mail') . ' ' . esc_html__('Join the Enjoy Naturally community', 'enjoy-naturally') . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<p>' . esc_html__('Seasonal inspiration, simple ideas, and nature-centered living — straight to your inbox.', 'enjoy-naturally') . '</p>';
	echo en_newsletter_form_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- form markup escaped in the builder.
	echo '</aside>';
}

/**
 * About-page sun and leaf lockup.
 *
 * @return string
 */
function en_lockup_html( $atts = array(), $content = null, $tag = '' ) {
	unset( $atts, $content, $tag );
	ob_start();
	?>
	<div class="lockup">
		<div class="lockup-mark">
			<img src="<?php echo esc_url(en_asset('logo-mark.svg')); ?>" alt="" width="92" height="80">
		</div>
		<p class="lockup-name">enjoy naturally</p>
		<p class="tagline"><?php echo esc_html__('Live well', 'enjoy-naturally'); ?> <span class="dot" aria-hidden="true">·</span> <?php echo esc_html__('Eat real', 'enjoy-naturally'); ?> <span class="dot" aria-hidden="true">·</span> <?php echo esc_html__('Enjoy more', 'enjoy-naturally'); ?></p>
	</div>
	<?php
	return (string) ob_get_clean();
}

/**
 * Fallback primary menu, used until a menu is assigned.
 */
function en_primary_menu_fallback( $args = array() ) {
	unset( $args );
	echo '<ul>';
	foreach (en_primary_links() as $item) {
		en_echo_nav_item($item['label'], en_nav_url($item), en_nav_is_current($item));
	}
	echo '</ul>';
}

/**
 * Fallback footer category list.
 */
function en_footer_explore_fallback( $args = array() ) {
	unset( $args );
	echo '<ul>';
	foreach (en_explore_links() as $slug => $label) {
		en_echo_nav_item($label, en_category_url($slug), is_category($slug));
	}
	echo '</ul>';
}

/**
 * Fallback footer page list.
 */
function en_footer_publication_fallback( $args = array() ) {
	unset( $args );
	echo '<ul>';
	foreach (en_publication_links() as $slug => $label) {
		en_echo_nav_item($label, en_page_url($slug), is_page($slug));
	}
	echo '</ul>';
}

/**
 * One list item.
 *
 * @param string $label   Link text.
 * @param string $url     URL.
 * @param bool   $current Whether it is the current page.
 */
function en_echo_nav_item($label, $url, $current) {
	echo '<li><a href="' . esc_url($url) . '"' . ( $current ? ' aria-current="page"' : '' ) . '>' . esc_html($label) . '</a></li>';
}

/**
 * Calendar notes under a category archive.
 *
 * @param string $slug Category slug.
 */
function en_render_calendar($slug) {
	$notes = en_calendar_for_category($slug);
	echo '<section aria-labelledby="calendar-title">';
	echo '<h2 id="calendar-title">' . esc_html__('On the seasonal calendar', 'enjoy-naturally') . '</h2>';
	echo '<div class="story-list">';
	if (! $notes) {
		echo '<p class="empty">' . wp_kses(
			sprintf(
				/* translators: %s: seasonal category URL */
				__('Nothing else is scheduled here yet. The <a href="%s">November hub</a> shows what the month is holding.', 'enjoy-naturally'),
				esc_url(en_category_url('seasonal'))
			),
			array( 'a' => array( 'href' => array() ) )
		) . '</p>';
	} else {
		foreach ($notes as $key => $item) {
			echo '<article class="calendar-block" id="' . esc_attr($key) . '">';
			echo '<p class="eyebrow">' . esc_html($item['kicker'] . ' · ' . __('In the works', 'enjoy-naturally')) . '</p>';
			echo '<h3>' . esc_html($item['title']) . '</h3>';
			echo '<p>' . esc_html($item['excerpt']) . '</p>';
			echo '</article>';
		}
	}
	echo '</div></section>';
}
