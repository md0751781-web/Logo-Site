<?php
/**
 * Order (artwork upload) and Contact forms.
 *
 * Submissions are saved as private "Requests" in wp-admin and emailed to the
 * notification address. Uploaded artwork is stored outside the public media
 * library (uploads/ldemb-private, web access denied) with a random file name
 * and can only be downloaded by logged-in editors.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LDEMB_MAX_UPLOAD', 10 * MB_IN_BYTES );

/**
 * Allowed artwork types: extension => accepted MIME types (checked with finfo).
 */
function ldemb_artwork_types() {
	return array(
		'png'  => array( 'image/png' ),
		'jpg'  => array( 'image/jpeg' ),
		'jpeg' => array( 'image/jpeg' ),
		'svg'  => array( 'image/svg+xml', 'image/svg', 'text/xml', 'application/xml', 'text/plain' ),
		'pdf'  => array( 'application/pdf' ),
	);
}

function ldemb_register_request_type() {
	register_post_type(
		'ldemb_request',
		array(
			'labels'          => array(
				'name'          => __( 'Requests', 'ld-embroidery' ),
				'singular_name' => __( 'Request', 'ld-embroidery' ),
				'edit_item'     => __( 'Request', 'ld-embroidery' ),
				'search_items'  => __( 'Search requests', 'ld-embroidery' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 20,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'ldemb_register_request_type' );

/**
 * Line-separated Customizer list → array of trimmed, non-empty values.
 */
function ldemb_lines( $key ) {
	return array_values( array_filter( array_map( 'trim', explode( "\n", (string) ldemb_mod( $key ) ) ) ) );
}

function ldemb_notify_email() {
	$email = ldemb_mod( 'notify_email' );
	return is_email( $email ) ? $email : get_option( 'admin_email' );
}

function ldemb_private_dir() {
	$dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'ldemb-private';
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}
	if ( ! file_exists( $dir . '/.htaccess' ) ) {
		file_put_contents( $dir . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}
	return $dir;
}

/**
 * Hidden fields shared by both forms.
 */
function ldemb_form_fields( $form ) {
	wp_nonce_field( 'ldemb_' . $form, '_ldemb_nonce' );
	echo '<input type="hidden" name="ldemb_return" value="' . esc_url( get_permalink() ) . '">';
	// Honeypot: real visitors never see or fill this.
	echo '<div class="hp-field" aria-hidden="true"><label>Website <input type="text" name="ldemb_website" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * Status message after redirect. Returns array( type, message ) or null.
 */
function ldemb_form_status( $form ) {
	if ( empty( $_GET['ldemb_form'] ) || $form !== $_GET['ldemb_form'] || empty( $_GET['ldemb_status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return null;
	}
	$code = sanitize_key( $_GET['ldemb_status'] ); // phpcs:ignore WordPress.Security.NonceVerification

	if ( 'sent' === $code ) {
		return array( 'success', ldemb_mod( 'order' === $form ? 'order_success' : 'ct_success' ) );
	}

	$errors = array(
		'expired'  => __( 'Your session expired. Please reload the page and try again.', 'ld-embroidery' ),
		'missing'  => __( 'Please fill in all required fields with a valid email address.', 'ld-embroidery' ),
		'nofile'   => __( 'Please choose your artwork file.', 'ld-embroidery' ),
		'filesize' => __( 'The file is larger than 10 MB. Please upload a smaller file.', 'ld-embroidery' ),
		'filetype' => __( 'This file type is not supported. Please upload PNG, JPG, SVG or PDF.', 'ld-embroidery' ),
		'upload'   => __( 'The file could not be uploaded. Please try again or contact us by email.', 'ld-embroidery' ),
		'limit'    => __( 'Too many submissions in a short time. Please wait a few minutes and try again.', 'ld-embroidery' ),
	);
	return array( 'error', isset( $errors[ $code ] ) ? $errors[ $code ] : __( 'Something went wrong. Please try again.', 'ld-embroidery' ) );
}

/**
 * Form target: admin-post.php with the action in the URL (a hidden field named "action" would shadow form.action in JS).
 */
function ldemb_form_action( $form ) {
	return add_query_arg( 'action', 'ldemb_' . $form, admin_url( 'admin-post.php' ) );
}

/**
 * PHP discards the whole POST when it exceeds post_max_size, so the form arrives empty.
 */
function ldemb_post_too_large() {
	return empty( $_POST ) && isset( $_SERVER['CONTENT_LENGTH'] ) && (int) $_SERVER['CONTENT_LENGTH'] > 0; // phpcs:ignore WordPress.Security.NonceVerification
}

function ldemb_form_redirect( $form, $status ) {
	$return = isset( $_POST['ldemb_return'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['ldemb_return'] ) ), home_url( '/' ) ) : ( wp_get_referer() ? wp_get_referer() : home_url( '/' ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$url    = add_query_arg(
		array(
			'ldemb_form'   => $form,
			'ldemb_status' => $status,
		),
		$return
	);
	wp_safe_redirect( $url . '#ldemb-' . $form );
	exit;
}

/**
 * Basic per-visitor rate limit: 5 submissions per 10 minutes.
 */
function ldemb_rate_limited() {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'ldemb_rl_' . md5( $ip );
	$hit = (int) get_transient( $key );
	if ( $hit >= 5 ) {
		return true;
	}
	set_transient( $key, $hit + 1, 10 * MINUTE_IN_SECONDS );
	return false;
}

function ldemb_post_text( $key, $textarea = false ) {
	if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return '';
	}
	$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	return $textarea ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
}

/**
 * Validate and store the uploaded artwork. Returns array( stored_name, original_name ) or an error code.
 */
function ldemb_store_artwork() {
	if ( empty( $_FILES['artwork'] ) || ! isset( $_FILES['artwork']['error'] ) ) {
		return 'nofile';
	}
	$file = $_FILES['artwork']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

	if ( UPLOAD_ERR_NO_FILE === $file['error'] ) {
		return 'nofile';
	}
	if ( in_array( $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) || $file['size'] > LDEMB_MAX_UPLOAD ) {
		return 'filesize';
	}
	if ( UPLOAD_ERR_OK !== $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) ) {
		return 'upload';
	}

	$original = sanitize_file_name( $file['name'] );
	$ext      = strtolower( pathinfo( $original, PATHINFO_EXTENSION ) );
	$types    = ldemb_artwork_types();
	if ( ! isset( $types[ $ext ] ) ) {
		return 'filetype';
	}
	if ( function_exists( 'finfo_open' ) ) {
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime  = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );
		if ( ! in_array( $mime, $types[ $ext ], true ) ) {
			return 'filetype';
		}
	}

	$stored = wp_generate_password( 24, false ) . '.' . $ext;
	if ( ! move_uploaded_file( $file['tmp_name'], ldemb_private_dir() . '/' . $stored ) ) {
		return 'upload';
	}
	return array( $stored, $original );
}

/**
 * Save a request post and email the owner.
 *
 * @param string $kind   order|contact.
 * @param string $title  Post title.
 * @param array  $fields Label => value pairs.
 * @param array  $meta   Extra meta.
 */
function ldemb_save_request( $kind, $title, $fields, $meta = array() ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'ldemb_request',
			'post_status' => 'publish',
			'post_title'  => $title,
		)
	);
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		return false;
	}

	update_post_meta( $post_id, '_ldemb_kind', $kind );
	update_post_meta( $post_id, '_ldemb_fields', $fields );
	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	$body = '';
	foreach ( $fields as $label => $value ) {
		$body .= $label . ': ' . $value . "\n";
	}
	$body .= "\n" . __( 'View in WordPress:', 'ld-embroidery' ) . ' ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";

	$headers = array();
	$email   = isset( $fields[ __( 'Email', 'ld-embroidery' ) ] ) ? $fields[ __( 'Email', 'ld-embroidery' ) ] : '';
	if ( is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . $email;
	}
	/* translators: 1: site name, 2: request title */
	wp_mail( ldemb_notify_email(), sprintf( '[%1$s] %2$s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $title ), $body, $headers );

	return $post_id;
}

function ldemb_handle_order() {
	if ( ldemb_post_too_large() ) {
		ldemb_form_redirect( 'order', 'filesize' );
	}
	if ( ! isset( $_POST['_ldemb_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_ldemb_nonce'] ), 'ldemb_order' ) ) {
		ldemb_form_redirect( 'order', 'expired' );
	}
	if ( '' !== ldemb_post_text( 'ldemb_website' ) ) {
		ldemb_form_redirect( 'order', 'error' );
	}

	$name     = ldemb_post_text( 'name' );
	$email    = sanitize_email( ldemb_post_text( 'email' ) );
	$whatsapp = ldemb_post_text( 'whatsapp' );
	$size     = ldemb_post_text( 'size' );
	$notes    = ldemb_post_text( 'notes', true );
	$category = ldemb_post_text( 'category' );
	$type     = ldemb_post_text( 'type' );

	$categories = ldemb_lines( 'order_categories' );
	$types      = ldemb_lines( 'order_types' );

	if ( ! $name || ! is_email( $email ) || ( $categories && ! in_array( $category, $categories, true ) ) || ( $types && ! in_array( $type, $types, true ) ) ) {
		ldemb_form_redirect( 'order', 'missing' );
	}
	if ( ldemb_rate_limited() ) {
		ldemb_form_redirect( 'order', 'limit' );
	}

	$file = ldemb_store_artwork();
	if ( ! is_array( $file ) ) {
		ldemb_form_redirect( 'order', $file );
	}

	$fields = array(
		__( 'Name', 'ld-embroidery' )             => $name,
		__( 'Email', 'ld-embroidery' )            => $email,
		__( 'WhatsApp', 'ld-embroidery' )         => $whatsapp,
		__( 'Design category', 'ld-embroidery' )  => $category,
		__( 'Embroidery type', 'ld-embroidery' )  => $type,
		__( 'Logo size', 'ld-embroidery' )        => $size,
		__( 'Notes', 'ld-embroidery' )            => $notes,
		__( 'Artwork file', 'ld-embroidery' )     => $file[1],
	);

	/* translators: 1: customer name, 2: design category */
	$title = sprintf( __( 'Order request: %1$s (%2$s)', 'ld-embroidery' ), $name, $category ? $category : __( 'no category', 'ld-embroidery' ) );
	$saved = ldemb_save_request(
		'order',
		$title,
		$fields,
		array(
			'_ldemb_file'          => $file[0],
			'_ldemb_original_name' => $file[1],
		)
	);

	if ( ! $saved ) {
		wp_delete_file( ldemb_private_dir() . '/' . $file[0] );
		ldemb_form_redirect( 'order', 'error' );
	}
	ldemb_form_redirect( 'order', 'sent' );
}
add_action( 'admin_post_nopriv_ldemb_order', 'ldemb_handle_order' );
add_action( 'admin_post_ldemb_order', 'ldemb_handle_order' );

function ldemb_handle_contact() {
	if ( ldemb_post_too_large() ) {
		ldemb_form_redirect( 'contact', 'filesize' );
	}
	if ( ! isset( $_POST['_ldemb_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_ldemb_nonce'] ), 'ldemb_contact' ) ) {
		ldemb_form_redirect( 'contact', 'expired' );
	}
	if ( '' !== ldemb_post_text( 'ldemb_website' ) ) {
		ldemb_form_redirect( 'contact', 'error' );
	}

	$name    = ldemb_post_text( 'name' );
	$email   = sanitize_email( ldemb_post_text( 'email' ) );
	$message = ldemb_post_text( 'message', true );

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		ldemb_form_redirect( 'contact', 'missing' );
	}
	if ( ldemb_rate_limited() ) {
		ldemb_form_redirect( 'contact', 'limit' );
	}

	$fields = array(
		__( 'Name', 'ld-embroidery' )    => $name,
		__( 'Email', 'ld-embroidery' )   => $email,
		__( 'Message', 'ld-embroidery' ) => $message,
	);
	/* translators: %s: sender name */
	$saved = ldemb_save_request( 'contact', sprintf( __( 'Message from %s', 'ld-embroidery' ), $name ), $fields );

	ldemb_form_redirect( 'contact', $saved ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_ldemb_contact', 'ldemb_handle_contact' );
add_action( 'admin_post_ldemb_contact', 'ldemb_handle_contact' );

/**
 * Admin-only artwork download.
 */
function ldemb_download_artwork() {
	$post_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) || ! check_admin_referer( 'ldemb_artwork_' . $post_id ) ) {
		wp_die( esc_html__( 'You are not allowed to download this file.', 'ld-embroidery' ), 403 );
	}

	$stored = basename( (string) get_post_meta( $post_id, '_ldemb_file', true ) );
	$path   = ldemb_private_dir() . '/' . $stored;
	if ( ! $stored || ! is_file( $path ) ) {
		wp_die( esc_html__( 'File not found.', 'ld-embroidery' ), 404 );
	}

	$name = sanitize_file_name( (string) get_post_meta( $post_id, '_ldemb_original_name', true ) );
	nocache_headers();
	header( 'Content-Type: application/octet-stream' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Content-Disposition: attachment; filename="' . ( $name ? $name : $stored ) . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_ldemb_artwork', 'ldemb_download_artwork' );

/**
 * Request details meta box + list columns.
 */
function ldemb_request_meta_box() {
	add_meta_box( 'ldemb_request_details', __( 'Request details', 'ld-embroidery' ), 'ldemb_render_request', 'ldemb_request', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ldemb_request_meta_box' );

function ldemb_render_request( $post ) {
	$fields = (array) get_post_meta( $post->ID, '_ldemb_fields', true );
	echo '<table class="widefat striped"><tbody>';
	foreach ( $fields as $label => $value ) {
		echo '<tr><th style="width:180px">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';

	if ( get_post_meta( $post->ID, '_ldemb_file', true ) ) {
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=ldemb_artwork&id=' . $post->ID ), 'ldemb_artwork_' . $post->ID );
		echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Download artwork', 'ld-embroidery' ) . '</a></p>';
	}
}

function ldemb_request_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => __( 'Request', 'ld-embroidery' ),
		'ldemb_kind' => __( 'Type', 'ld-embroidery' ),
		'ldemb_from' => __( 'Email', 'ld-embroidery' ),
		'date'       => $columns['date'],
	);
}
add_filter( 'manage_ldemb_request_posts_columns', 'ldemb_request_columns' );

function ldemb_request_column( $column, $post_id ) {
	if ( 'ldemb_kind' === $column ) {
		echo 'order' === get_post_meta( $post_id, '_ldemb_kind', true ) ? esc_html__( 'Order', 'ld-embroidery' ) : esc_html__( 'Message', 'ld-embroidery' );
	} elseif ( 'ldemb_from' === $column ) {
		$fields = (array) get_post_meta( $post_id, '_ldemb_fields', true );
		$email  = isset( $fields[ __( 'Email', 'ld-embroidery' ) ] ) ? $fields[ __( 'Email', 'ld-embroidery' ) ] : '';
		echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—';
	}
}
add_action( 'manage_ldemb_request_posts_custom_column', 'ldemb_request_column', 10, 2 );

/**
 * Delete the stored artwork together with its request.
 */
function ldemb_delete_request_file( $post_id ) {
	if ( 'ldemb_request' !== get_post_type( $post_id ) ) {
		return;
	}
	$stored = basename( (string) get_post_meta( $post_id, '_ldemb_file', true ) );
	if ( $stored ) {
		wp_delete_file( ldemb_private_dir() . '/' . $stored );
	}
}
add_action( 'before_delete_post', 'ldemb_delete_request_file' );
