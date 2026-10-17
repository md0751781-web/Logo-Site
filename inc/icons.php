<?php
/**
 * Inline SVG icons (outline style, currentColor).
 *
 * @package LD_Embroidery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an inline SVG icon. Icons are decorative (aria-hidden) unless a label is given.
 *
 * @param string $name  Icon name.
 * @param string $class Extra CSS class.
 */
function ldemb_icon( $name, $class = '' ) {
	$small = array(
		'check'    => '<circle cx="12" cy="12" r="9"/><path d="m8 12.5 2.7 2.7L16.2 9.5"/>',
		'shield'   => '<path d="M12 3 5 6v5.5c0 4.4 3 8 7 9.5 4-1.5 7-5.1 7-9.5V6z"/><path d="m9 12 2.2 2.2L15.5 10"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
		'lock'     => '<rect x="5" y="10.5" width="14" height="10" rx="2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/>',
		'star'     => '<path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9z"/>',
		'user'     => '<circle cx="12" cy="8.5" r="4"/><path d="M4.5 20.5c1.2-3.8 4-5.5 7.5-5.5s6.3 1.7 7.5 5.5"/>',
		'cart'     => '<path d="M3 4h2.2l2.3 11.2a1.5 1.5 0 0 0 1.5 1.2h8.6a1.5 1.5 0 0 0 1.5-1.1L21 8H6.2"/><circle cx="9.5" cy="20" r="1.3"/><circle cx="17.5" cy="20" r="1.3"/>',
		'upload'   => '<path d="M12 15V4m0 0L7.5 8.5M12 4l4.5 4.5"/><path d="M4 15v3.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V15"/>',
		'arrow'    => '<path d="M5 12h14m0 0-5-5m5 5-5 5"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 3.8 5.5 3.8 9s-1.3 6.5-3.8 9c-2.5-2.5-3.8-5.5-3.8-9S9.5 5.5 12 3z"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
		'support'  => '<path d="M4 13v-1a8 8 0 0 1 16 0v1"/><rect x="3" y="13" width="4" height="6" rx="1.5"/><rect x="17" y="13" width="4" height="6" rx="1.5"/><path d="M19 19c0 1.5-1.5 2.5-4 2.5h-2"/>',
		'bolt'     => '<path d="M13 3 5 13.5h6L10 21l8-10.5h-6z"/>',
	);

	$large = array(
		'cap'    => '<path d="M7 31c0-11 8-19 18-19s17 7.5 17 17.5V31z"/><path d="M25 12c-4.5 4.5-6.5 11-6.5 19"/><path d="M7 31h36.5a2.5 2.5 0 0 1 0 5H15c-4.5 0-8-1.8-8-5z"/><circle cx="25" cy="11" r="1.6"/><path d="M11.5 26.5h27" stroke-dasharray="2 2.6"/>',
		'puff'   => '<rect x="6" y="14" width="30" height="24" rx="7"/><path d="M13 14v-1a7 7 0 0 1 7-7h15a7 7 0 0 1 7 7v15a7 7 0 0 1-6 7"/><path d="M12.5 20.5h5a3 3 0 0 1 0 6h-3.5 3.5a3.2 3.2 0 0 1 0 6.5h-5M23.5 20.5V33h3.5a6.25 6.25 0 0 0 0-12.5z"/><path d="M10 34.5h22" stroke-dasharray="2 2.6"/>',
		'tshirt' => '<path d="M17.5 6 8 10.5 3.5 20l6.5 3 3-4.5V42h22V18.5l3 4.5 6.5-3L40 10.5 30.5 6c-1 3-3.6 5-6.5 5s-5.5-2-6.5-5z"/><circle cx="24" cy="24" r="5" stroke-dasharray="2 2.3"/><path d="M13 38h22" stroke-dasharray="2 2.6"/>',
		'jacket' => '<path d="M17 6 9.5 9.5c-2 1-3.2 2.6-3.6 4.6L3 41h7l2.2-15.5V43h23.6V25.5L38 41h7l-2.9-26.9c-.4-2-1.6-3.6-3.6-4.6L31 6c-1.2 2.6-4 4.5-7 4.5S18.2 8.6 17 6z"/><path d="m24 18.5 1.9 3.9 4.3.6-3.1 3 .7 4.3-3.8-2-3.8 2 .7-4.3-3.1-3 4.3-.6z"/><circle cx="24" cy="25" r="9" stroke-dasharray="2 2.4"/>',
	);

	if ( isset( $small[ $name ] ) ) {
		$paths = $small[ $name ];
		$view  = '0 0 24 24';
		$sw    = '1.8';
	} elseif ( isset( $large[ $name ] ) ) {
		$paths = $large[ $name ];
		$view  = '0 0 48 48';
		$sw    = '2';
	} else {
		return '';
	}

	$fill = 'star' === $name ? 'currentColor' : 'none';

	return sprintf(
		'<svg class="icon icon-%1$s %2$s" viewBox="%3$s" fill="%4$s" stroke="currentColor" stroke-width="%5$s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%6$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		$view,
		$fill,
		$sw,
		$paths
	);
}
