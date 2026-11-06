<?php
/**
 * Search results.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

get_header();
?>
<main id="main">
	<header class="page-hero wide">
		<p class="eyebrow"><?php esc_html_e('Search', 'enjoy-naturally'); ?></p>
		<h1>
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: search query */
					__('Stories matching “%s”', 'enjoy-naturally'),
					get_search_query()
				)
			);
			?>
		</h1>
		<form class="signup" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
			<div class="signup-row">
				<label class="field">
					<span class="visually-hidden"><?php esc_html_e('Search stories', 'enjoy-naturally'); ?></span>
					<input type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" required>
				</label>
				<button class="button" type="submit"><?php esc_html_e('Search', 'enjoy-naturally'); ?></button>
			</div>
		</form>
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
			<p class="empty"><?php esc_html_e('Nothing matches that yet. The journal is just getting started.', 'enjoy-naturally'); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
