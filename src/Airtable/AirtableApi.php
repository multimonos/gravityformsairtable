<?php

namespace GFAirtable\Airtable;

use GFAirtable\AirtableAddon;

class AirtableApi
{
    const BASEURI = 'https://api.airtable.com/v0';

    protected static function params( array $args = [] ) {
        $defaults = [
            'headers' => [
                'Content-type'  => 'application/json',
                'Authorization' => "Bearer " . AirtableAddon::get_instance()->get_plugin_setting( 'api_key' ),
            ]
        ];
        return array_merge( $defaults, $args );
    }

    public static function get_bases() {
        return wp_remote_get( self::BASEURI . '/meta/bases/', self::params() );
    }

    public static function get_bases_schema( $baseid ) {
        return wp_remote_get( self::BASEURI . "/meta/bases/{$baseid}/tables/", self::params() );
    }

    public static function create_record( $base_id, $table_id, $fields ) {
        return wp_remote_post( self::BASEURI . "/{$base_id}/{$table_id}", self::params( [
            'body' => json_encode( [
                'fields'   => $fields,
                'typecast' => true,
            ] )
        ] ) );
    }

    public static function create_records( $base_id, $table_id, $records ) {
        // NOTE: airtable has a hard limit of 10 records for bulk processing
        return wp_remote_post( self::BASEURI . "/{$base_id}/{$table_id}", self::params( [
            'body' => json_encode( [
                'records'               => $records,
                'returnFieldsByFieldId' => true,
                'typecast'              => true,
            ] )
        ] ) );
    }
}