<?php

namespace GFAirtable\UserInterface;

use GFAirtable\Models\FeedPeer;

class FeedList
{
    public static function svelte_html() {
        return "<div id='gf-airtable-app'></div>";
    }

    public static function add_action_links( array $links ) {
        $links['airtable_sync'] = '<a class="gf-airtable-action" data-airtable-action="sync-feed" data-airtable-feed="_id_" href="#"">Synchronize</a>';
        $links['airtable_reset'] = '<a class="gf-airtable-action" data-airtable-action="reset-feed" data-airtable-feed="_id_" href="#">Reset</a>';
        return $links;
    }

    public static function get_columns() {
        return [
            'feedName'    => esc_html__( 'Name', 'gravityformsairtable' ),
            'sync_status' => esc_html( 'Synced', 'airtablefeedaddon' ),
        ];
    }

    public static function get_column_value_sync_status( $feed ) {
        $total = FeedPeer::count_entries($feed);
        $synced = FeedPeer::count_synced_entries($feed);

        return $synced. ' of ' . $total;
    }
}