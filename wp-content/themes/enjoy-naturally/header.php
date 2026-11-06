<?php
/**
 * Site header.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'enjoy-naturally'); ?></a>
<header class="site-header">
	<div class="header-inner">
		<a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
			<?php echo en_logo_lockup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme logo SVG. ?>
		</a>
		<div class="header-tools">
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
				<span class="nav-toggle-bars" aria-hidden="true"></span>
				<span class="visually-hidden"><?php esc_html_e('Menu', 'enjoy-naturally'); ?></span>
			</button>
			<nav id="site-nav" class="site-nav" aria-label="<?php esc_attr_e('Primary', 'enjoy-naturally'); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => '',
						'items_wrap'     => '<ul>%3$s</ul>',
						'fallback_cb'    => 'en_primary_menu_fallback',
						'depth'          => 1,
					)
				);
				?>
			</nav>
			<button class="icon-button search-open" type="button" aria-haspopup="dialog" aria-controls="search-dialog">
				<?php echo en_icon('search'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG. ?>
				<span class="visually-hidden"><?php esc_html_e('Search stories', 'enjoy-naturally'); ?></span>
			</button>
			<noscript>
				<a class="icon-button" href="<?php echo esc_url(home_url('/?s=')); ?>"><?php esc_html_e('Search', 'enjoy-naturally'); ?></a>
			</noscript>
		</div>
	</div>
</header>
<noscript><style>.site-nav{display:block;position:static;background:transparent;border:0;box-shadow:none;padding:0}.site-nav ul{display:flex;flex-wrap:wrap;gap:.15rem 1rem}.nav-toggle{display:none}</style></noscript>
