<?php
/**
 * Homepage: Layout 3 seasonal stories.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

get_header();
$hero = en_hero_image();
?>
<main id="main">
	<section class="hero">
		<div>
			<h1><?php echo esc_html(en_mod('en_hero_title')); ?></h1>
			<p class="lede"><?php echo esc_html(en_mod('en_hero_lede')); ?></p>
			<a class="button" href="<?php echo esc_url(en_hero_cta_url()); ?>"><?php echo esc_html(en_mod('en_hero_cta_label')); ?> <?php echo en_icon('arrow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
		<figure class="hero-photo">
			<img src="<?php echo esc_url($hero['url']); ?>" alt="<?php echo esc_attr($hero['alt']); ?>" width="<?php echo esc_attr((string) $hero['width']); ?>" height="<?php echo esc_attr((string) $hero['height']); ?>" fetchpriority="high" decoding="async">
		</figure>
	</section>
	<?php foreach (en_home_bands() as $band) : ?>
		<?php en_render_band($band); ?>
	<?php endforeach; ?>
	<section class="closing" aria-label="<?php esc_attr_e('Recipe and newsletter', 'enjoy-naturally'); ?>">
		<?php en_render_recipe_panel(); ?>
		<?php en_render_newsletter_panel(); ?>
	</section>
</main>
<?php
get_footer();
