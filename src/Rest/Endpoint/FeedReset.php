<?php

namespace GFAirtable\Rest\Endpoint;

use GFAirtable\AirtableAddon;
use GFAirtable\Models\FeedEntry;
use GFAirtable\Models\FeedPeer;
use GFAirtable\Rest\Validator\NonceValidator;
use GFAirtable\Models\FeedSyncStatus;

class FeedReset implements Endpoint
{
    public function handle( \WP_REST_Request $request ) {
        $nonceValidator = new NonceValidator();
        if ( ! $nonceValidator->is_valid( $request ) ) {
            return $nonceValidator->error();
        }

        $feed_id = $request->get_param( 'feed_id' );
        $feed = AirtableAddon::get_instance()->get_feed( $feed_id );

        $entries = FeedPeer::get_synced_entries( $feed, null, ['page_size' => 25] );

        $entry_responses = array_map( function( $entry ) use ( $feed ) {
            $feed_entry = new FeedEntry( $feed, $entry );
            $r = $feed_entry->delete( 'record_id' );

            $msg = ['entry_id' => $entry['id']];

            if ( $r === false ) {
                $msg['status'] = 422;
            } else {
                $msg['status'] = 200;
            }

            return $msg;

        }, $entries );

        $feed_sync_status = new FeedSyncStatus( $feed_id );

        return new \WP_REST_Response( [
            'entries'          => $entry_responses,
            'feed_sync_status' => $feed_sync_status->to_array(),
        ], 202);
    }

}