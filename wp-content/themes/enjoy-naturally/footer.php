<?php
/**
 * Site footer, search dialog, and scripts.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;
?>
<footer class="site-footer">
	<div class="footer-inner">
		<div>
			<p class="footer-name">enjoy naturally</p>
			<p class="tagline"><?php esc_html_e('Live well', 'enjoy-naturally'); ?> <span class="dot" aria-hidden="true">·</span> <?php esc_html_e('Eat real', 'enjoy-naturally'); ?> <span class="dot" aria-hidden="true">·</span> <?php esc_html_e('Enjoy more', 'enjoy-naturally'); ?></p>
			<p class="disclosure"><?php esc_html_e('Notes from my life at home. Not medical advice.', 'enjoy-naturally'); ?></p>
		</div>
		<nav class="footer-nav" aria-label="<?php esc_attr_e('Journal', 'enjoy-naturally'); ?>">
			<h2><?php esc_html_e('Journal', 'enjoy-naturally'); ?></h2>
			<ul>
				<?php foreach (en_journal_entries() as $entry) : ?>
					<li><a href="<?php echo esc_url($entry['url']); ?>"><?php echo esc_html($entry['title']); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<nav class="footer-nav" aria-label="<?php esc_attr_e('Categories', 'enjoy-naturally'); ?>">
			<h2><?php esc_html_e('Explore', 'enjoy-naturally'); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer_explore',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'fallback_cb'    => 'en_footer_explore_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<nav class="footer-nav" aria-label="<?php esc_attr_e('Publication', 'enjoy-naturally'); ?>">
			<h2><?php esc_html_e('Publication', 'enjoy-naturally'); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer_publication',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'fallback_cb'    => 'en_footer_publication_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>
	</div>
	<div class="footer-bar">
		<p><?php echo esc_html(sprintf(__('© %s Enjoy Naturally. All rights reserved.', 'enjoy-naturally'), wp_date('Y'))); ?></p>
		<p><a href="<?php echo esc_url(en_page_url('privacy')); ?>"><?php esc_html_e('Privacy', 'enjoy-naturally'); ?></a> · <a href="<?php echo esc_url(en_page_url('contact')); ?>"><?php esc_html_e('Contact', 'enjoy-naturally'); ?></a></p>
	</div>
</footer>
<dialog class="search-dialog" id="search-dialog" aria-labelledby="search-title">
	<div class="search-panel">
		<div class="search-head">
			<h2 id="search-title"><?php esc_html_e('Search stories', 'enjoy-naturally'); ?></h2>
			<button class="icon-button search-close" type="button">
				<span class="visually-hidden"><?php esc_html_e('Close search', 'enjoy-naturally'); ?></span>
				<span aria-hidden="true">×</span>
			</button>
		</div>
		<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
			<label class="field" for="search-input">
				<span class="visually-hidden"><?php esc_html_e('Search stories', 'enjoy-naturally'); ?></span>
				<input id="search-input" type="search" name="s" placeholder="<?php esc_attr_e('Try freeze-dried, snacks, or garden', 'enjoy-naturally'); ?>" autocomplete="off">
			</label>
		</form>
		<p id="search-status" class="form-note" role="status"></p>
		<ul id="search-results" class="search-results"></ul>
	</div>
</dialog>
<?php en_print_search_index(); ?>
<?php wp_footer(); ?>
</body>
</html>
