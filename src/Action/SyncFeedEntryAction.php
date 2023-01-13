<?php

namespace GFAirtable\Action;

use GFAirtable\AirtableAddon;
use GFAirtable\Models\FeedEntry;

class SyncFeedEntryAction
{
    const ID = 'gravityformsairtable_sync_feed_entry';

    public static function run() {
        $addon = AirtableAddon::get_instance();
        $entry = \GFAPI::get_entry( $_REQUEST['entry_id'] );
        $form = \GFAPI::get_form( $_REQUEST['form_id'] );
        $feed = $addon->get_feed( $_REQUEST['feed_id'] );
        $redirect = $_REQUEST['r'];
        $force = (bool)$_REQUEST['force'] ?? false;

        if ( ! is_array( $entry ) || ! is_array( $feed ) && ! is_array( $form ) ) {
            wp_redirect( $redirect );
            exit;
        }

        $feed_entry = new FeedEntry( $feed, $entry );
        $record_id = $feed_entry->get_record_id();

        if ( ! empty( $record_id ) && $force === false ) {
            $note = 'Record already exists in Airtable Base ' . $feed['meta']['base_id'] . '/' . $feed['meta']['table_id'] . ' as Record #' . $record_id;
            $addon->add_feed_error( $note, $feed, $entry, $form );
            wp_redirect( $redirect );
            exit;
        }

        $rs = $addon->process_feed( $feed, $entry, $form );

        if ( is_wp_error( $rs ) ) {
            // something error like
        }

        wp_redirect( $redirect );
        exit;
    }
}