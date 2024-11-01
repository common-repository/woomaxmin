<?php
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit;
	}
    delete_option('woomaxmin_settings');
    $all_user_ids = get_users( 'fields=ID' );
    foreach ( $all_user_ids as $user_id ) {
        delete_user_meta( $user_id, 'woomaxmin_orderId' );
        delete_user_meta( $user_id, 'woomaxmin_total' );
    }	
?>