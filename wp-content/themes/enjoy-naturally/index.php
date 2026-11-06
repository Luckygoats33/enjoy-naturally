<?php
/**
 * Fallback story list.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="main">
	<header class="page-hero wide">
		<p class="eyebrow"><?php esc_html_e('Journal', 'enjoy-naturally'); ?></p>
		<h1><?php esc_html_e('All stories', 'enjoy-naturally'); ?></h1>
	</header>
	<div class="page-wrap wide tight">
		<?php if (have_posts()) : ?>
			<div class="story-list">
				<?php
				while (have_posts()) :
					the_post();
					get_template_part('template-parts/story');
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __('Previous', 'enjoy-naturally'),
					'next_text' => __('Next', 'enjoy-naturally'),
				)
			);
			?>
		<?php else : ?>
			<p class="empty"><?php esc_html_e('No posts on this page yet.', 'enjoy-naturally'); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
