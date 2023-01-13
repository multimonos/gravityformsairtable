<?php

namespace GFAirtable\Rest\Endpoint;

use GFAirtable\AirtableAddon;
use GFAirtable\Models\FeedEntry;
use GFAirtable\Rest\Validator\NonceValidator;

class EntrySync implements Endpoint
{
    public function handle( \WP_REST_Request $request ) {
        $nonceValidator = new NonceValidator();
        if ( ! $nonceValidator->is_valid( $request ) ) {
            return $nonceValidator->error();
        }

        $feed_id = $request->get_param( 'feed_id' );
        $entry_id = $request->get_param( 'entry_id' );

        // feed
        $feed = \GFAPI::get_feed( $feed_id );
        if ( is_wp_error( $feed ) ) {
            return new \WP_Error( $feed->get_error_code(), $feed->get_error_message(), ['status' => 404] );
        }

        // entry
        $entry = \GFAPI::get_entry( $entry_id );
        if ( is_wp_error( $entry ) ) {
            return new \WP_Error( $entry->get_error_code(), $entry->get_error_message(), ['status' => 404] );
        }

        $feed_entry = new FeedEntry( $feed, $entry );

        // form
        $form = $feed_entry->get_form();
        if ( is_wp_error( $form ) ) {
            return new \WP_Error( $form->get_error_code(), $form->get_error_message(), ['status' => 404] );
        }

        // process entry
        $rs = AirtableAddon::get_instance()->process_feed( $feed, $entry, $form );

        if ( is_wp_error( $rs ) ) {
            return new \WP_Error( $rs->get_error_code(), $rs->get_error_message(), ['status' => 400] );
        }

        return new \WP_REST_Response( [
            'feed_id'  => $feed_id,
            'entry_id' => $entry_id,
            'airtable' => [
                'record_id' => $feed_entry->get_record_id(),
            ]
        ], ['status' => 202] );
    }

}