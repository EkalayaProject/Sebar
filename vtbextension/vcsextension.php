<?php
/**
 * Prevent the plugin file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, uh?' );
define( 'VCSEXTENSION_PLUGIN_ASSETS_URL', plugins_url( 'assets', __FILE__ ) );

class VcsExtension {
	public function __construct() {

	}
} new VcsExtension();
