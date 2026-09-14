<?php

add_filter( 'ohio-importer/append-license-info', function( $import_file_info, $import_types ) {
	$purchase_code = get_option( 'ohio_license_code', false );
	if ( $purchase_code ) {
		foreach ( $import_types as $import_type ) {
			if ( ! empty( $import_file_info[$import_type] ) ) {
				$import_file_info[$import_type] .= '/' . $purchase_code;
			}
		}
	}

	return $import_file_info;
}, 10, 2);

add_filter( 'ohio/ui-license-info', function( $license_info ) {
	$license_info['is_active'] = get_option( 'ohio_license_code', false );
	$license_info['created'] = get_option( 'ohio_license_sold_at', '-' );
	$license_info['secret_key'] = get_option( 'ohio_license_code', '-' );
	$license_info['buyer'] = get_option( 'ohio_buyer_username', '' );
	$license_info['url'] = home_url();
	$license_info['type'] = 'envato';

	return $license_info;
}, 10);

add_filter( 'ohio/has-license', function ( $is ) {
	return get_option( 'ohio_license_code', false ) !== false;
}, 10);

add_filter( 'ohio/get-figma-url', function( ) {
	return 'https://demo.clbthemes.com/get_figma';
});
