<?php

namespace GFAirtable\Rest\Endpoint;

use GFAirtable\AirtableAddon;
use GFAirtable\Models\FeedSyncStatus;
use GFAirtable\Rest\Validator\NonceValidator;

class Feed implements Endpoint
{
    public function handle( \WP_REST_Request $request ) {
        $nonceValidator = new NonceValidator();
        if ( ! $nonceValidator->is_valid( $request ) ) {
            return $nonceValidator->error();
        }

        $feed_id = $request->get_param( 'feed_id' );
        $feed = AirtableAddon::get_instance()->get_feed( $feed_id );
        $feed['sync_status']= (new FeedSyncStatus( $feed_id ))->to_array();

        return new \WP_REST_Response( [
            'feed'             => $feed,
        ], 200 );
    }
}