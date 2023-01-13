<?php

namespace GFAirtable\Rest\Endpoint;

use GFAirtable\Models\FeedEntry;
use GFAirtable\NonceValidator;

class FeedEntryMeta implements Endpoint
{
    public function handle( \WP_REST_Request $request ) {
        $nonceValidator = new NonceValidator();
        if ( ! $nonceValidator->isValid( $request ) ) {
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

        // feed entry
        $feed_entry = new FeedEntry( $feed, $entry );

        return new \WP_REST_Response( [
            'feed_id'  => $feed_id,
            'entry_id' => $entry_id,
            'airtable' => [
                'record_id' => $feed_entry->get_record_id(),
            ]
        ], ['status' => 200] );
    }
}