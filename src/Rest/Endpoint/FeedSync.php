<?php

namespace GFAirtable\Rest\Endpoint;

use GFAirtable\Action\SyncFeedEntriesAction;
use GFAirtable\AirtableAddon;
use GFAirtable\Models\FeedPeer;
use GFAirtable\Models\FeedSyncStatus;
use GFAirtable\Rest\Validator\NonceValidator;

class FeedSync implements Endpoint
{
    public function handle( \WP_REST_Request $request ) {
        $nonceValidator = new NonceValidator();
        if ( ! $nonceValidator->is_valid( $request ) ) {
            return $nonceValidator->error();
        }

        $feed_id = $request->get_param( 'feed_id' );
        $feed = AirtableAddon::get_instance()->get_feed( $feed_id );

        $form = \GFAPI::get_form( $feed['form_id'] );
        $entries = FeedPeer::get_unsynced_entries( $feed, null, ['page_size' => 10] );


        // process feed entries
        $rs = [];
        if ( count( $entries ) ) {
            $action = new SyncFeedEntriesAction();
            // airtable has a hard limit of 10 entries per requeset
            $rs = $action->sync( $form, $feed, $entries );
        }

        $feed_sync_status = new FeedSyncStatus( $feed['id'] );

        //result
        return new \WP_REST_Response( [
            'records'          => $rs,
            'feed_sync_status' => $feed_sync_status->to_array(),
        ], 202 );
    }
}