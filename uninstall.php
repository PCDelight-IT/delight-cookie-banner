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
delete_option( 'pcdelicoba_settings' );

// Optional: also remove any transient or cached data if added later.
// delete_transient( 'pcdelicoba_xyz_cache' );

// Optional: remove site-wide settings in multisite environments.
if ( is_multisite() ) {
	$pcdelicoba_sites = get_sites( [ 'fields' => 'ids' ] );
	foreach ( $pcdelicoba_sites as $pcdelicoba_blog_id ) {
		switch_to_blog( $pcdelicoba_blog_id );
		delete_option( 'pcdelicoba_settings' );
		restore_current_blog();
	}
}
