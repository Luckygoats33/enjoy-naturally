<?php
/**
 * Newsletter and contact forms.
 *
 * Entries are private Reader notes. A full-page cache can keep the HTML,
 * so the forms do not embed a nonce. They use a honeypot, a rate limit,
 * and a same-site check instead.
 *
 * @package Enjoy_Naturally
 */

defined('ABSPATH') || exit;

add_action('init', 'en_register_submission_type');
add_action('wp_ajax_en_newsletter', 'en_ajax_newsletter');
add_action('wp_ajax_nopriv_en_newsletter', 'en_ajax_newsletter');
add_action('wp_ajax_en_contact', 'en_ajax_contact');
add_action('wp_ajax_nopriv_en_contact', 'en_ajax_contact');
add_action('admin_post_en_newsletter', 'en_post_newsletter');
add_action('admin_post_nopriv_en_newsletter', 'en_post_newsletter');
add_action('admin_post_en_contact', 'en_post_contact');
add_action('admin_post_nopriv_en_contact', 'en_post_contact');
add_action('add_meta_boxes', 'en_submission_meta_box_register');
add_filter('manage_en_submission_posts_columns', 'en_submission_columns');
add_action('manage_en_submission_posts_custom_column', 'en_submission_column', 10, 2);
add_shortcode('enjoy_newsletter_form', 'en_newsletter_form_html');
add_shortcode('enjoy_contact_form', 'en_contact_form_html');
add_shortcode('enjoy_lockup', 'en_lockup_html');

/**
 * Private inbox for signup addresses and contact notes.
 */
function en_register_submission_type() {
	register_post_type(
		'en_submission',
		array(
			'labels'              => array(
				'name'          => __('Reader notes', 'enjoy-naturally'),
				'singular_name' => __('Reader note', 'enjoy-naturally'),
				'menu_name'     => __('Reader notes', 'enjoy-naturally'),
				'all_items'     => __('Reader notes', 'enjoy-naturally'),
				'edit_item'     => __('Read note', 'enjoy-naturally'),
				'search_items'  => __('Search notes', 'enjoy-naturally'),
				'not_found'     => __('No notes yet.', 'enjoy-naturally'),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
			'query_var'           => false,
			'rewrite'             => false,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-email',
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
			'supports'            => array( 'title', 'editor' ),
			'delete_with_user'    => false,
		)
	);
}

/**
 * Contact topics, in the order shown on the form.
 *
 * @return string[]
 */
function en_contact_topics() {
	return array(
		__('Correction', 'enjoy-naturally'),
		__('Story idea', 'enjoy-naturally'),
		__('Partnership', 'enjoy-naturally'),
		__('Something else', 'enjoy-naturally'),
	);
}

/**
 * Newsletter form, filterable so a Klaviyo embed can replace it.
 *
 * @return string
 */
function en_newsletter_form_html( $atts = array(), $content = null, $tag = '' ) {
	unset( $atts, $content, $tag );
	$html = en_form_html('newsletter');
	return apply_filters('enjoy_naturally_newsletter_form_html', $html);
}

/**
 * Contact form, filterable so a forms plugin can replace it.
 *
 * @return string
 */
function en_contact_form_html( $atts = array(), $content = null, $tag = '' ) {
	unset( $atts, $content, $tag );
	$html = en_form_html('contact');
	return apply_filters('enjoy_naturally_contact_form_html', $html);
}

/**
 * Shared form shell.
 *
 * @param string $kind newsletter|contact.
 * @return string
 */
function en_form_html($kind) {
	$notice  = en_form_notice($kind);
	$success = is_array($notice) && ! empty($notice['ok']);
	$error   = is_array($notice) && empty($notice['ok']) ? $notice['message'] : '';
	$heading = $success && ! empty($notice['heading']) ? $notice['heading'] : __('Saved.', 'enjoy-naturally');
	$message = $success && ! empty($notice['message']) ? $notice['message'] : '';
	$action  = 'newsletter' === $kind ? 'en_newsletter' : 'en_contact';
	$anchor  = 'newsletter' === $kind ? 'en-newsletter' : 'en-contact';

	ob_start();
	echo '<div class="en-form" id="' . esc_attr($anchor) . '">';
	echo '<form class="signup" data-form="' . esc_attr($kind) . '" data-endpoint="' . esc_url(admin_url('admin-ajax.php')) . '" method="post" action="' . esc_url(admin_url('admin-post.php')) . '" novalidate';
	echo $success ? ' hidden' : '';
	echo '>';
	echo '<p class="form-error" role="alert"' . ( '' === $error ? ' hidden' : '' ) . '>' . esc_html($error) . '</p>';
	echo '<input type="hidden" name="action" value="' . esc_attr($action) . '">';
	echo '<input type="hidden" name="en_redirect" value="' . esc_url(en_form_redirect_target()) . '">';
	echo '<div class="en-hp" aria-hidden="true"><label>' . esc_html__('Company', 'enjoy-naturally') . '<input type="text" name="en_hp" tabindex="-1" autocomplete="off" value=""></label></div>';

	if ('contact' === $kind) {
		en_contact_fields();
	} else {
		en_newsletter_fields();
	}

	echo '</form>';
	echo '<div class="success"' . ( $success ? '' : ' hidden' ) . '>';
	echo '<h3>' . esc_html($heading) . '</h3>';
	echo '<p>' . esc_html($message) . '</p>';
	echo '</div></div>';
	return (string) ob_get_clean();
}

/**
 * Newsletter fields.
 */
function en_newsletter_fields() {
	?>
	<div class="signup-row">
		<label class="field">
			<span class="visually-hidden"><?php esc_html_e('Email address', 'enjoy-naturally'); ?></span>
			<input name="email" type="email" required autocomplete="email" maxlength="180" placeholder="<?php esc_attr_e('Your email address', 'enjoy-naturally'); ?>" aria-describedby="newsletter-hint">
		</label>
		<button class="button" type="submit"><?php esc_html_e('Subscribe', 'enjoy-naturally'); ?></button>
	</div>
	<label class="checkbox">
		<input name="consent" type="checkbox" value="1" required>
		<span><?php esc_html_e('Send the note when there is one. Your address stays with me until a mailing list is connected. It is not sold.', 'enjoy-naturally'); ?></span>
	</label>
	<p id="newsletter-hint" class="form-note"><?php echo wp_kses_post(sprintf(__('No confirmation email yet. <a href="%s">How the list will work</a>.', 'enjoy-naturally'), esc_url(en_page_url('newsletter')))); ?></p>
	<?php
}

/**
 * Contact fields.
 */
function en_contact_fields() {
	?>
	<label class="field">
		<span><?php esc_html_e('Name', 'enjoy-naturally'); ?></span>
		<input name="name" type="text" required autocomplete="name" maxlength="80">
	</label>
	<label class="field">
		<span><?php esc_html_e('Email', 'enjoy-naturally'); ?></span>
		<input name="email" type="email" required autocomplete="email" maxlength="180">
	</label>
	<label class="field">
		<span><?php esc_html_e('Topic', 'enjoy-naturally'); ?></span>
		<select name="topic" required>
			<option value=""><?php esc_html_e('Choose one', 'enjoy-naturally'); ?></option>
			<?php foreach (en_contact_topics() as $topic) : ?>
				<option><?php echo esc_html($topic); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<label class="field">
		<span><?php esc_html_e('Message', 'enjoy-naturally'); ?></span>
		<textarea name="message" required maxlength="2000"></textarea>
	</label>
	<label class="checkbox">
		<input name="consent" type="checkbox" value="1" required>
		<span><?php esc_html_e('I understand this message comes to the person who writes Enjoy Naturally and is stored on the website.', 'enjoy-naturally'); ?></span>
	</label>
	<button class="button" type="submit"><?php esc_html_e('Send message', 'enjoy-naturally'); ?></button>
	<p class="form-note"><?php echo wp_kses_post(sprintf(__('Please do not include medical details or account passwords. Health questions belong with a clinician. See the <a href="%s">medical disclaimer</a>.', 'enjoy-naturally'), esc_url(en_page_url('medical-disclaimer')))); ?></p>
	<?php
}

/**
 * URL to return to after a no-JavaScript submit.
 *
 * @return string
 */
function en_form_redirect_target() {
	if (is_singular()) {
		return get_permalink();
	}
	if (is_front_page()) {
		return home_url('/');
	}
	global $wp;
	$request = ( isset($wp) && is_object($wp) && ! empty($wp->request) ) ? $wp->request : '';
	return home_url($request ? user_trailingslashit($request) : '/');
}

/**
 * One-time notice carried across the no-JavaScript redirect.
 *
 * @param string $kind newsletter|contact.
 * @return array<string, mixed>|null
 */
function en_form_notice($kind) {
	static $loaded = false;
	static $data   = null;
	if (! $loaded) {
		$loaded = true;
		if (! empty($_GET['en_notice'])) {
			$token = sanitize_key(wp_unslash($_GET['en_notice']));
			$found = get_transient('en_notice_' . $token);
			if (is_array($found)) {
				delete_transient('en_notice_' . $token);
				$data = $found;
			}
		}
	}
	if (! is_array($data) || ! isset($data['form']) || $data['form'] !== $kind) {
		return null;
	}
	return $data;
}

/**
 * AJAX: newsletter.
 */
function en_ajax_newsletter() {
	wp_send_json(en_process_form('newsletter'));
}

/**
 * AJAX: contact.
 */
function en_ajax_contact() {
	wp_send_json(en_process_form('contact'));
}

/**
 * No-JavaScript newsletter submit.
 */
function en_post_newsletter() {
	en_redirect_with_notice(en_process_form('newsletter'));
}

/**
 * No-JavaScript contact submit.
 */
function en_post_contact() {
	en_redirect_with_notice(en_process_form('contact'));
}

/**
 * Validate, store, and try to email a form.
 *
 * @param string $kind newsletter|contact.
 * @return array<string, mixed>
 */
function en_process_form($kind) {
	$honeypot = isset($_POST['en_hp']) ? trim(sanitize_text_field(wp_unslash($_POST['en_hp']))) : '';
	if ('' !== $honeypot) {
		return en_form_success($kind, true);
	}
	if (! en_request_is_same_site()) {
		return en_form_error($kind, __('The form could not tell that this came from the journal. Reload the page and try again.', 'enjoy-naturally'));
	}
	if (! en_rate_limit_ok($kind)) {
		return en_form_error($kind, __('Please wait a little while before sending another note.', 'enjoy-naturally'));
	}

	$email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
	$consent = isset($_POST['consent']) && '1' === sanitize_text_field(wp_unslash($_POST['consent']));
	if (! is_email($email)) {
		return en_form_error($kind, __('Use an email address I can reply to.', 'enjoy-naturally'));
	}
	if (! $consent) {
		return en_form_error($kind, __('The consent box is required.', 'enjoy-naturally'));
	}

	$name    = '';
	$topic   = '';
	$message = '';
	if ('contact' === $kind) {
		$name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
		$name = function_exists('mb_substr') ? mb_substr($name, 0, 80) : substr($name, 0, 80);
		$topic_raw = isset($_POST['topic']) ? sanitize_text_field(wp_unslash($_POST['topic'])) : '';
		$topic     = in_array($topic_raw, en_contact_topics(), true) ? $topic_raw : '';
		$message   = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
		$message   = function_exists('mb_substr') ? mb_substr($message, 0, 2000) : substr($message, 0, 2000);
		if ('' === $name || '' === $topic || '' === trim($message)) {
			return en_form_error($kind, __('Name, email, topic, and message are all required.', 'enjoy-naturally'));
		}
	}

	$email = strtolower($email);
	if ('newsletter' === $kind && en_newsletter_exists($email)) {
		return array(
			'ok'      => true,
			'form'    => $kind,
			'heading' => __('That address is already saved.', 'enjoy-naturally'),
			'message' => __('I already have that address for the letter. Nothing was sent to your inbox today.', 'enjoy-naturally'),
		);
	}

	$title   = 'newsletter' === $kind ? $email : $name . ' — ' . $topic;
	$content = 'newsletter' === $kind
		? __('Consent to the seasonal note was checked.', 'enjoy-naturally')
		: $message;

	$post_id = wp_insert_post(
		wp_slash(
			array(
				'post_type'    => 'en_submission',
				'post_status'  => 'private',
				'post_title'   => $title,
				'post_content' => $content,
				'post_author'  => en_primary_admin_id(),
				'meta_input'   => array(
					'_en_kind'   => $kind,
					'_en_email'  => $email,
					'_en_name'   => $name,
					'_en_topic'  => $topic,
					'_en_mailed' => '0',
				),
			)
		),
		true
	);

	if (is_wp_error($post_id) || ! $post_id) {
		return en_form_error($kind, __('The journal could not save that just now. Please try again in a moment.', 'enjoy-naturally'));
	}

	$sent = en_mail_submission($kind, $email, $name, $topic, $message);
	update_post_meta($post_id, '_en_mailed', $sent ? '1' : '0');

	return en_form_success($kind, false);
}

/**
 * Success payload.
 *
 * @param string $kind      Form kind.
 * @param bool   $honeypot  True when the honeypot tripped and nothing was stored.
 * @return array<string, mixed>
 */
function en_form_success($kind, $honeypot) {
	unset($honeypot);
	if ('contact' === $kind) {
		return array(
			'ok'      => true,
			'form'    => $kind,
			'heading' => __('Saved.', 'enjoy-naturally'),
			'message' => __('I can read it in the site inbox.', 'enjoy-naturally'),
		);
	}
	return array(
		'ok'      => true,
		'form'    => $kind,
		'heading' => __('Saved.', 'enjoy-naturally'),
		'message' => __('I will use this address only for the letter. Nothing was sent to your inbox today.', 'enjoy-naturally'),
	);
}

/**
 * Error payload.
 *
 * @param string $kind    Form kind.
 * @param string $message Reader-facing message.
 * @return array<string, mixed>
 */
function en_form_error($kind, $message) {
	return array(
		'ok'      => false,
		'form'    => $kind,
		'message' => $message,
	);
}

/**
 * Whether this newsletter address is already stored.
 *
 * @param string $email Lowercase email.
 * @return bool
 */
function en_newsletter_exists($email) {
	global $wpdb;
	$found = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} k ON k.post_id = p.ID AND k.meta_key = '_en_kind' AND k.meta_value = 'newsletter'
			INNER JOIN {$wpdb->postmeta} e ON e.post_id = p.ID AND e.meta_key = '_en_email' AND e.meta_value = %s
			WHERE p.post_type = 'en_submission' AND p.post_status IN ('private','publish')
			LIMIT 1",
			$email
		)
	);
	return ! empty($found);
}

/**
 * Email the site admin. Failure is recorded on the note and not shown as an error.
 *
 * @param string $kind    Form kind.
 * @param string $email   Sender email.
 * @param string $name    Sender name.
 * @param string $topic   Contact topic.
 * @param string $message Contact message.
 * @return bool
 */
function en_mail_submission($kind, $email, $name, $topic, $message) {
	$to = apply_filters('enjoy_naturally_form_recipient', get_option('admin_email'), $kind);
	if (! is_email($to)) {
		return false;
	}
	if ('newsletter' === $kind) {
		$subject = __('Enjoy Naturally seasonal note signup', 'enjoy-naturally');
		$body    = sprintf(
			/* translators: %s: email address */
			__("A reader asked to join the seasonal letter.\n\nEmail: %s\n\nThe address is also saved under Reader notes.", 'enjoy-naturally'),
			$email
		);
	} else {
		$subject = sprintf(
			/* translators: 1: topic, 2: name */
			__('Enjoy Naturally: %1$s from %2$s', 'enjoy-naturally'),
			$topic,
			$name
		);
		$body = "Name: {$name}\nEmail: {$email}\nTopic: {$topic}\n\n{$message}\n";
	}
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $email,
	);
	return (bool) wp_mail($to, $subject, $body, $headers);
}

/**
 * Reject cross-site posts when the browser sends Origin or Referer.
 *
 * @return bool
 */
function en_request_is_same_site() {
	$allowed = array();
	$home    = wp_parse_url(home_url(), PHP_URL_HOST);
	if (is_string($home) && '' !== $home) {
		$allowed[] = en_host_key($home);
	}
	if (! empty($_SERVER['HTTP_HOST'])) {
		$http_host = preg_replace('/:\d+$/', '', wp_unslash($_SERVER['HTTP_HOST']));
		$allowed[] = en_host_key($http_host);
	}
	$allowed = array_values(array_unique(array_filter($allowed)));
	if (! $allowed) {
		return true;
	}

	$source = '';
	if (! empty($_SERVER['HTTP_ORIGIN'])) {
		$source = wp_parse_url(wp_unslash($_SERVER['HTTP_ORIGIN']), PHP_URL_HOST);
	} elseif (! empty($_SERVER['HTTP_REFERER'])) {
		$source = wp_parse_url(wp_unslash($_SERVER['HTTP_REFERER']), PHP_URL_HOST);
	} else {
		return true;
	}
	return is_string($source) && in_array(en_host_key($source), $allowed, true);
}

/**
 * Compare hosts without a leading www.
 *
 * @param string $host Hostname.
 * @return string
 */
function en_host_key($host) {
	$host = strtolower($host);
	if (0 === strpos($host, 'www.')) {
		$host = substr($host, 4);
	}
	return $host;
}

/**
 * Twenty notes an hour from one address is enough for a household.
 *
 * @param string $bucket Form kind.
 * @return bool
 */
function en_rate_limit_ok($bucket) {
	$ip    = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
	$key   = 'en_rl_' . md5($bucket . '|' . $ip);
	$count = (int) get_transient($key);
	if ($count >= 20) {
		return false;
	}
	set_transient($key, $count + 1, HOUR_IN_SECONDS);
	return true;
}

/**
 * First administrator, used as the note's author.
 *
 * @return int
 */
function en_primary_admin_id() {
	$admins = get_users(
		array(
			'role'    => 'administrator',
			'number'  => 1,
			'orderby' => 'ID',
			'fields'  => 'ID',
		)
	);
	return $admins ? (int) $admins[0] : 0;
}

/**
 * Send the reader back to the page that held the form.
 *
 * @param array<string, mixed> $result Process result.
 */
function en_redirect_with_notice($result) {
	$token = wp_generate_password(12, false, false);
	set_transient('en_notice_' . $token, $result, 2 * MINUTE_IN_SECONDS);

	$target = '';
	if (! empty($_POST['en_redirect'])) {
		$target = esc_url_raw(wp_unslash($_POST['en_redirect']));
	}
	if ('' === $target) {
		$target = wp_get_referer();
	}
	$target = $target ? strtok($target, '#') : home_url('/');
	$target = remove_query_arg('en_notice', $target);
	$target = add_query_arg('en_notice', $token, $target);
	$anchor = ( isset($result['form']) && 'contact' === $result['form'] ) ? 'en-contact' : 'en-newsletter';
	$target .= '#' . $anchor;

	wp_safe_redirect(wp_validate_redirect($target, home_url('/')));
	exit;
}

/**
 * Side box on a reader note.
 */
function en_submission_meta_box_register() {
	add_meta_box(
		'en_submission_details',
		__('Note details', 'enjoy-naturally'),
		'en_submission_meta_box',
		'en_submission',
		'side',
		'high'
	);
}

/**
 * Render note details.
 *
 * @param WP_Post $post Note.
 */
function en_submission_meta_box($post) {
	$rows = array(
		__('Kind', 'enjoy-naturally')       => get_post_meta($post->ID, '_en_kind', true),
		__('Email', 'enjoy-naturally')      => get_post_meta($post->ID, '_en_email', true),
		__('Name', 'enjoy-naturally')       => get_post_meta($post->ID, '_en_name', true),
		__('Topic', 'enjoy-naturally')      => get_post_meta($post->ID, '_en_topic', true),
		__('Email sent', 'enjoy-naturally') => '1' === get_post_meta($post->ID, '_en_mailed', true) ? __('Yes', 'enjoy-naturally') : __('No', 'enjoy-naturally'),
	);
	echo '<ul>';
	foreach ($rows as $label => $value) {
		$display = ( is_string($value) && '' !== $value ) ? $value : '—';
		echo '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html($display) . '</li>';
	}
	echo '</ul>';
}

/**
 * Inbox columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function en_submission_columns($columns) {
	$fresh = array();
	foreach ($columns as $key => $label) {
		$fresh[ $key ] = $label;
		if ('title' === $key) {
			$fresh['en_kind']   = __('Kind', 'enjoy-naturally');
			$fresh['en_email']  = __('Email', 'enjoy-naturally');
			$fresh['en_mailed'] = __('Email sent', 'enjoy-naturally');
		}
	}
	return $fresh;
}

/**
 * Inbox column values.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function en_submission_column($column, $post_id) {
	if ('en_kind' === $column) {
		echo esc_html((string) get_post_meta($post_id, '_en_kind', true));
	}
	if ('en_email' === $column) {
		echo esc_html((string) get_post_meta($post_id, '_en_email', true));
	}
	if ('en_mailed' === $column) {
		echo '1' === get_post_meta($post_id, '_en_mailed', true) ? esc_html__('Yes', 'enjoy-naturally') : esc_html__('No', 'enjoy-naturally');
	}
}
