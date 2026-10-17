<?php
/**
 * LD Embroidery theme functions.
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LDEMB_VERSION', '2.2.0' );
define( 'LDEMB_DIR', get_template_directory() );
define( 'LDEMB_URI', get_template_directory_uri() );

require LDEMB_DIR . '/inc/setup.php';
require LDEMB_DIR . '/inc/icons.php';
require LDEMB_DIR . '/inc/customizer.php';
require LDEMB_DIR . '/inc/woocommerce.php';
require LDEMB_DIR . '/inc/template-tags.php';
require LDEMB_DIR . '/inc/content-types.php';
require LDEMB_DIR . '/inc/requests.php';

if ( is_admin() ) {
	require LDEMB_DIR . '/inc/admin.php';
}
