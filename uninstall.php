<?php
/**
 * Uninstall routine for Delight Cookie Banner.
 *
 * This file runs only when the plugin is uninstalled (deleted),
 * not when it is merely deactivated.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Safety: check capability before removal.
if ( ! current_user_can( 'delete_plugins' ) ) {
	return;
}

// Remove all plugin options.
delete_option( 'dcb_settings' );

// Optional: also remove any transient or cached data if added later.
// delete_transient( 'dcb_xyz_cache' );

// Optional: remove site-wide settings in multisite environments.
if ( is_multisite() ) {
	$sites = get_sites( [ 'fields' => 'ids' ] );
	foreach ( $sites as $blog_id ) {
		switch_to_blog( $blog_id );
		delete_option( 'dcb_settings' );
		restore_current_blog();
	}
}
