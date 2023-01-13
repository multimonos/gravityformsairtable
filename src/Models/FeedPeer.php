<?php

namespace GFAirtable\Models;

class FeedPeer
{
    public static function count_entries( $feed ) {
        return \GFAPI::count_entries( $feed['form_id'] );
    }

    public static function count_synced_entries( $feed ) {
        $search = [
            'status'        => 'active',
            'field_filters' => [
                [
                    'key'      => FeedEntry::feed_metakey( $feed, 'record_id' ),
                    'value'    => '',
                    'operator' => 'isnot'
                ]
            ],
        ];

        return \GFAPI::count_entries( $feed['form_id'], $search );
    }

    public static function get_unsynced_entries( $feed, $sort = null, $paging = [] ) {
        $search = [
            'status'        => 'active',
            'field_filters' => [
                [
                    'key'      => FeedEntry::feed_metakey( $feed, 'record_id' ),
                    'value'    => '',
                    'operator' => 'is'
                ]
            ],
        ];

        $default_paging = ['offset' => 0, 'page_size' => 200];
        $paging = array_merge( $default_paging, $paging );

        return \GFAPI::get_entries( $feed['form_id'], $search, $sort, $paging );
    }

    public static function get_synced_entries( $feed, $sort = null, $paging = [] ) {
        $search = [
            'field_filters' => [
                [
                    'key'      => FeedEntry::feed_metakey( $feed, 'record_id' ),
                    'value'    => '',
                    'operator' => 'isnot'
                ]
            ],
        ];

        $default_paging = ['offset' => 0, 'page_size' => 200];
        $paging = array_merge( $default_paging, $paging );

        return \GFAPI::get_entries( $feed['form_id'], $search, $sort, $paging );
    }
}