<?php
/**
 * Plugin Name:     Gravity Forms Airtable Add-On
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Sync gravity forms data to Airtable
 * Author:          Craig Hopgood
 * Author URI:      YOUR SITE HERE
 * Text Domain:     gravityformsairtable
 * Domain Path:     /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
    die;
}


add_action( 'gform_loaded', function() {
    if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
        return;
    }


    \GFForms::include_feed_addon_framework();
    require_once __DIR__ . '/vendor/autoload.php';

    \GFAddOn::register( \GFAirtable\AirtableAddon::class );
    add_action( 'wp_ajax_' . \GFAirtable\Action\SyncFeedEntryAction::ID, [\GFAirtable\Action\SyncFeedEntryAction::class, 'run'], 10 );

}, 5 );
