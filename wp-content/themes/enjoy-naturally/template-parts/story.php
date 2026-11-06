<?php
/**
 * One story in an archive list.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;
?>
<article <?php post_class(has_post_thumbnail() ? 'story' : 'story story-text'); ?>>
	<?php if (has_post_thumbnail()) : ?>
		<a href="<?php the_permalink(); ?>">
			<?php
			$thumb_id = get_post_thumbnail_id();
			$alt      = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
			if (! is_string($alt) || '' === $alt) {
				$alt = get_the_title();
			}
			the_post_thumbnail(
				'en-story',
				array(
					'loading' => 'lazy',
					'alt'     => $alt,
				)
			);
			?>
		</a>
	<?php endif; ?>
	<div>
		<p class="eyebrow"><?php echo esc_html(en_kicker()); ?></p>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>
	</div>
</article>
