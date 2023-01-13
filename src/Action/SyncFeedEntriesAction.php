<?php

namespace GFAirtable\Action;

use GFAirtable\Airtable\AirtableApi;
use GFAirtable\Airtable\Models\AirtableMeta;
use GFAirtable\AirtableAddon;
use GFAirtable\Models\FeedEntry;

class SyncFeedEntriesAction
{
    public function sync( $form, $feed, array $entries ) {
        // prepare
        list( $baseid, $tableid ) = explode( "|", $feed['meta']['base_table_ids'] );
        $schema = AirtableMeta::get()->base_table( $baseid, $tableid );

        // create the airtable records payload
        $gf_records = $this->create_airtable_records( $form, $feed, $entries );

        // send payload to airtable to create records
        $res = AirtableApi::create_records( $baseid, $tableid, $gf_records );

        // mark each gravity forms entry with the airtable record_id
        if ( ! is_wp_error( $res ) && $res['response']['code'] === 200 ) {

            $body = wp_remote_retrieve_body( $res );
            $airtable = json_decode( $body, true );
            $entry_ids = array_column( $entries, 'id' );
            $airtable_pkey = $schema['primaryFieldId'];

            // mark
            foreach ( $airtable['records'] as $record ) {

//            array_map( function( $record ) use ( $feed, $entries, $pkey ) {
                $entry_id = $record['fields'][$airtable_pkey];
                $idx = array_search( $entry_id, $entry_ids );

                $entry = $idx === false
                    ? \GFAPI::get_entry( $entry_id ) // don't query db unless we need to
                    : $entries[$idx];

                // add entry metadata so we can update next time instead of create
                $feed_entry = new FeedEntry( $feed, $entry );
                $feed_entry->set_record_id( $record['id'] );

                // ux note ... too slow
                // AirtableAddon::get_instance()->add_note( $entry_id, $feed['meta']['feedName'] . ' Record saved', 'success' );

//            }, $synced['records'] );

            }
            return $airtable['records'];
        }


        // mark each entry with the error
        array_map(
            fn( $entry ) => AirtableAddon::get_instance()->add_feed_error( json_encode( $res ), $feed, $entry, $form ),
            $entries
        );

        return $res;
    }

    public function create_airtable_records( $form, $feed, array $entries ) {
        // init
        $fieldmap = $feed['meta']['fieldmap'];

        $records = [];

        foreach ( $entries as $entry ) {
            $record = [];

            // fields
            $record['fields'] = array_reduce(
                $fieldmap,
                function( $acc, $field ) use ( $form, $entry ) {
                    $id = $field['key'];
                    $value = AirtableAddon::get_instance()->get_field_value( $form, $entry, $field['value'] );
                    $acc[$id] = $value;
                    return $acc;
                }, [] );

            // record_id ( optional )
            $feed_entry = new FeedEntry( $feed, $entry );
            $record_id = $feed_entry->get_record_id();

            if ( $record_id !== false ) {
                $record['id'] = $record_id;
            }

            $records[] = $record;
        }

        return $records;
    }

}