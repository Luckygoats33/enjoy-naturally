<?php
/**
 * Missing pages.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="main">
	<header class="page-hero">
		<p class="eyebrow"><?php esc_html_e('Missing page', 'enjoy-naturally'); ?></p>
		<h1><?php esc_html_e('That page is not in the garden yet.', 'enjoy-naturally'); ?></h1>
		<p class="lede"><?php esc_html_e('The link may be old, or the story may still be on the calendar. Try the November homepage, or search the stories we have published.', 'enjoy-naturally'); ?></p>
		<p><a class="button" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to the homepage', 'enjoy-naturally'); ?></a></p>
	</header>
</main>
<?php
get_footer();
