<?php
/**
 * Publication pages.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="main">
	<?php
	while (have_posts()) :
		the_post();
		?>
		<header class="page-hero">
			<?php
			en_breadcrumbs(
				array(
					array(
						'label' => __('Home', 'enjoy-naturally'),
						'url'   => home_url('/'),
					),
					array(
						'label' => get_the_title(),
					),
				)
			);
			?>
			<p class="eyebrow"><?php echo esc_html(en_kicker()); ?></p>
			<h1><?php the_title(); ?></h1>
		</header>
		<div class="page-wrap prose">
			<?php
			if (post_password_required()) {
				echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				the_content();
				$extra = en_supplemental_copy((string) get_post_field('post_name'));
				if ('' !== $extra) {
					echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd in the helper.
				}
			}
			?>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
