<?php
/**
 * Single story.
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
		$minutes  = en_reading_minutes();
		$category = get_the_category();
		$crumbs   = array(
			array(
				'label' => __('Home', 'enjoy-naturally'),
				'url'   => home_url('/'),
			),
		);
		if ($category) {
			$crumbs[] = array(
				'label' => $category[0]->name,
				'url'   => get_category_link($category[0]),
			);
		}
		$crumbs[] = array( 'label' => get_the_title() );
		$recipe   = 'Recipe of the week' === en_kicker() ? ' recipe-card' : '';
		?>
		<article <?php post_class('article-wrap'); ?>>
			<?php en_breadcrumbs($crumbs); ?>
			<header class="article-head">
				<p class="eyebrow"><?php echo esc_html(en_kicker()); ?></p>
				<h1><?php the_title(); ?></h1>
				<p class="byline">
					<time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('F j, Y')); ?></time>
					· <?php echo esc_html(sprintf(_n('%d min read', '%d min read', $minutes, 'enjoy-naturally'), $minutes)); ?>
					<?php if ($category) : ?>
						· <a href="<?php echo esc_url(get_category_link($category[0])); ?>"><?php echo esc_html($category[0]->name); ?></a>
					<?php endif; ?>
				</p>
			</header>
			<?php if (has_post_thumbnail()) : ?>
				<?php
				$thumb_id = get_post_thumbnail_id();
				$caption  = wp_get_attachment_caption($thumb_id);
				$alt      = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
				if (! is_string($alt) || '' === $alt) {
					$alt = get_the_title();
				}
				?>
				<figure class="article-figure">
					<?php
					echo wp_get_attachment_image(
						$thumb_id,
						'large',
						false,
						array(
							'alt'     => $alt,
							'loading' => 'eager',
						)
					);
					?>
					<?php if ($caption) : ?>
						<figcaption><?php echo esc_html($caption); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
			<?php if (post_password_required()) : ?>
				<div class="prose"><?php echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php else : ?>
				<div class="prose<?php echo esc_attr($recipe); ?>">
					<?php
					the_content();
					$extra = en_supplemental_copy((string) get_post_field('post_name'));
					if ('' !== $extra) {
						echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd in the helper.
					}
					?>
				</div>
				<?php
				$related = en_related_posts(get_the_ID(), 2);
				if ($related) :
					?>
					<aside class="related">
						<h2><?php esc_html_e('Keep reading', 'enjoy-naturally'); ?></h2>
						<div class="related-list">
							<?php foreach ($related as $related_post) : ?>
								<?php en_render_card_post($related_post); ?>
							<?php endforeach; ?>
						</div>
					</aside>
				<?php endif; ?>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
