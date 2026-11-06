<?php
/**
 * Homepage hero controls.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

add_action('customize_register', 'en_customize_register');

/**
 * Defaults for the seasonal hero. An empty CTA URL uses the Seasonal category.
 *
 * @return array<string, string>
 */
function en_mod_defaults() {
	return array(
		'en_hero_title'     => __('This week at home', 'enjoy-naturally'),
		'en_hero_lede'      => __('Soup for tonight, an 8-minute yoga flow, and one room reset.', 'enjoy-naturally'),
		'en_hero_cta_label' => __('See this week', 'enjoy-naturally'),
		'en_hero_cta_url'   => '',
	);
}

/**
 * Theme mod with a default when the value has never been saved.
 *
 * @param string $key Setting key.
 * @return string
 */
function en_mod($key) {
	$defaults = en_mod_defaults();
	$default  = isset($defaults[ $key ]) ? $defaults[ $key ] : '';
	$value    = get_theme_mod($key, null);
	if (null === $value || false === $value || '' === $value) {
		return $default;
	}
	return (string) $value;
}

/**
 * Button target. Falls back to the Seasonal category.
 *
 * @return string
 */
function en_hero_cta_url() {
	$url = en_mod('en_hero_cta_url');
	if ('' !== $url) {
		return $url;
	}
	return home_url('/#this-week');
}

/**
 * Hero photograph. The theme image is used until a media library image is chosen.
 *
 * @return array<string, mixed>
 */
function en_hero_image() {
	$fallback = array(
		'url'    => en_asset('images/hero-november.jpg'),
		'width'  => 1600,
		'height' => 1200,
		'alt'    => __('A woman and two children arranging carrots, greens, and squash in a wide bowl at a wooden kitchen table.', 'enjoy-naturally'),
	);
	$id       = absint(get_theme_mod('en_hero_image', 0));
	if (! $id) {
		return $fallback;
	}
	$src = wp_get_attachment_image_src($id, 'full');
	if (! $src) {
		return $fallback;
	}
	$alt = get_post_meta($id, '_wp_attachment_image_alt', true);
	return array(
		'url'    => $src[0],
		'width'  => (int) $src[1],
		'height' => (int) $src[2],
		'alt'    => ( is_string($alt) && '' !== $alt ) ? $alt : $fallback['alt'],
	);
}

/**
 * Keep the hero control pointed at a real image.
 *
 * @param mixed $value Raw setting.
 * @return int
 */
function en_sanitize_image_id($value) {
	$id = absint($value);
	if (! $id) {
		return 0;
	}
	$mime = get_post_mime_type($id);
	if (! is_string($mime) || 0 !== strpos($mime, 'image/')) {
		return 0;
	}
	return $id;
}

/**
 * Customizer section.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function en_customize_register($wp_customize) {
	$wp_customize->add_section(
		'enjoy_naturally_hero',
		array(
			'title'       => __('Homepage hero', 'enjoy-naturally'),
			'description' => __('The seasonal headline at the top of the homepage. The story bands underneath are the November layout in the theme.', 'enjoy-naturally'),
			'priority'    => 30,
		)
	);

	$wp_customize->add_setting(
		'en_hero_title',
		array(
			'default'           => en_mod_defaults()['en_hero_title'],
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'en_hero_title',
		array(
			'label'   => __('Headline', 'enjoy-naturally'),
			'section' => 'enjoy_naturally_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'en_hero_lede',
		array(
			'default'           => en_mod_defaults()['en_hero_lede'],
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'en_hero_lede',
		array(
			'label'   => __('Intro', 'enjoy-naturally'),
			'section' => 'enjoy_naturally_hero',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'en_hero_cta_label',
		array(
			'default'           => en_mod_defaults()['en_hero_cta_label'],
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'en_hero_cta_label',
		array(
			'label'   => __('Button label', 'enjoy-naturally'),
			'section' => 'enjoy_naturally_hero',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'en_hero_cta_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'en_hero_cta_url',
		array(
			'label'       => __('Button link', 'enjoy-naturally'),
			'description' => __('Leave blank to use the Seasonal category.', 'enjoy-naturally'),
			'section'     => 'enjoy_naturally_hero',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'en_hero_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'en_sanitize_image_id',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'en_hero_image',
			array(
				'label'     => __('Hero photograph', 'enjoy-naturally'),
				'section'   => 'enjoy_naturally_hero',
				'mime_type' => 'image',
			)
		)
	);
}
