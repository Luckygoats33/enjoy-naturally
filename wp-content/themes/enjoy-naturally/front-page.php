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
	<section class="this-week" id="this-week" aria-labelledby="this-week-title">
		<h2 id="this-week-title"><?php esc_html_e('This week', 'enjoy-naturally'); ?></h2>
		<div class="this-week-grid">
			<a class="week-card" href="<?php echo esc_url(home_url('/creamy-wild-rice-mushroom-soup/')); ?>">
				<?php echo en_img('soup-week.jpg', __('Creamy wild rice and mushroom soup beside bread.', 'enjoy-naturally'), 1600, 1200); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="eyebrow"><?php esc_html_e('Recipe', 'enjoy-naturally'); ?></p>
				<strong><?php esc_html_e('Wild rice and mushroom soup', 'enjoy-naturally'); ?></strong>
				<span><?php esc_html_e('Cook tonight', 'enjoy-naturally'); ?> <?php echo en_icon('arrow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
			<a class="week-card" href="<?php echo esc_url(home_url('/mindful-living/#cooler-room-flow')); ?>">
				<?php echo en_img('home-cozy.jpg', __('A quiet corner of the living room for a short practice.', 'enjoy-naturally'), 1600, 1200); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="eyebrow"><?php esc_html_e('Yoga', 'enjoy-naturally'); ?></p>
				<strong><?php esc_html_e('Cooler Room Flow, 8 minutes', 'enjoy-naturally'); ?></strong>
				<span><?php esc_html_e('Practice now', 'enjoy-naturally'); ?> <?php echo en_icon('arrow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
			<a class="week-card" href="<?php echo esc_url(home_url('/natural-home/#evening-reset')); ?>">
				<?php echo en_img('home-cozy.jpg', __('A sofa with a knit throw and a lamp.', 'enjoy-naturally'), 1600, 1200); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="eyebrow"><?php esc_html_e('Home', 'enjoy-naturally'); ?></p>
				<strong><?php esc_html_e('Evening reset, five steps', 'enjoy-naturally'); ?></strong>
				<span><?php esc_html_e('Do this week', 'enjoy-naturally'); ?> <?php echo en_icon('arrow'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
		</div>
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
