<?php

namespace GFAirtable\Models;

use GFAirtable\Models\FeedPeer;

class FeedSyncStatus
{
    protected $data = [
        'id'     => null,
        'total'  => null,
        'synced' => null,
    ];

    public function __construct( $feed_id ) {
        $feed = \GFAPI::get_feed( $feed_id );

        $total = FeedPeer::count_entries( $feed );
        $synced = FeedPeer::count_synced_entries( $feed );
        $unsynced = $total - $synced;

        $this->data = [
            'id'     => $feed['id'],
            'total'  => $total,
            'synced' => $synced,
            'unsynced' => $unsynced,
        ];
    }

    public function to_array() {
        return $this->data;
    }
}