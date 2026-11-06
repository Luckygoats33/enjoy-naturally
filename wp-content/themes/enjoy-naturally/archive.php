<?php
/**
 * Category, tag, date, and author archives.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

get_header();

$category = is_category() ? get_queried_object() : null;
?>
<main id="main">
	<header class="page-hero wide">
		<?php
		$crumbs = array(
			array(
				'label' => __('Home', 'enjoy-naturally'),
				'url'   => home_url('/'),
			),
			array(
				'label' => wp_strip_all_tags(get_the_archive_title()),
			),
		);
		en_breadcrumbs($crumbs);
		?>
		<p class="eyebrow"><?php echo esc_html($category instanceof WP_Term ? __('Category', 'enjoy-naturally') : __('Journal', 'enjoy-naturally')); ?></p>
		<h1><?php echo esc_html(wp_strip_all_tags(get_the_archive_title())); ?></h1>
		<?php if (! $category) : ?>
			<?php the_archive_description('<p class="lede">', '</p>'); ?>
		<?php endif; ?>
	</header>
	<div class="page-wrap wide tight">
		<?php if ($category instanceof WP_Term) : ?>
			<?php $hub = en_category_hub($category->slug); ?>
			<?php if ('' !== $hub) : ?>
				<div class="prose category-hub"><?php echo wp_kses_post($hub); ?></div>
			<?php elseif ('' !== trim((string) $category->description)) : ?>
				<p class="lede"><?php echo wp_kses_post(en_expand_root_urls($category->description)); ?></p>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (have_posts()) : ?>
			<section aria-labelledby="published-title">
				<h2 id="published-title"><?php esc_html_e('More posts', 'enjoy-naturally'); ?></h2>
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
			</section>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
