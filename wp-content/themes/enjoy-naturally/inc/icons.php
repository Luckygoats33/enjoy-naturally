<?php
/**
 * Inline icons copied from the static Enjoy Naturally preview.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

/**
 * Return a decorative SVG.
 *
 * @param string $name leaf|arrow|search|mail.
 * @return string
 */
/**
 * The sun-and-leaf wordmark from assets/logo.svg, inlined so Fraunces applies.
 *
 * @return string
 */
function en_logo_lockup() {
	static $svg = null;
	if (null === $svg) {
		$path = get_template_directory() . '/assets/logo.svg';
		$raw  = is_readable($path) ? file_get_contents($path) : '';
		$svg  = str_replace('<svg ', '<svg class="brand-logo" focusable="false" ', (string) $raw);
	}
	return $svg;
}

function en_icon($name) {
	$icons = array(
		'leaf'   => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21c-1-6-6-9-8-14 5 1 8 6 8 14z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 21c1-5 6-8 10-11-5 2-8 6-9 11z" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M12 21V8" stroke="currentColor" stroke-width="1.6"/></svg>',
		'arrow'  => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'search' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M16 16l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'mail'   => '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 7l8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>',
	);

	return isset($icons[ $name ]) ? $icons[ $name ] : '';
}
