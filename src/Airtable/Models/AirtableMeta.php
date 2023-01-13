<?php

namespace GFAirtable\Airtable\Models;

use GFAirtable\Airtable\AirtableApi;
use Cactus\UserLocation\Debug;

class AirtableMeta
{
    protected static $instance = null;

    protected $bases = null;

    protected function __construct() {
    }

    public static function get() {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function bases() {
        // lazy loading
        if ( $this->bases === null ) {
            $this->bases = $this->fetch_bases();
        }
        return $this->bases;
    }

    public function base( $id ) {
        $bases = $this->bases();
        $idx = array_search( $id, array_column( $bases, 'id' ) );
        return $idx === false
            ? false
            : $bases[$idx];
    }

    public function base_table( $baseid, $tableid ) {
        $base = $this->base( $baseid );

        if ( $base === false ) {
            return false;
        }

        $idx = array_search( $tableid, array_column( $base['schema']['tables'], 'id' ) );
        return $idx ===false
            ? false
            : $base['schema']['tables'][$idx];
    }

    protected function fetch_bases() {
        $rs = AirtableApi::get_bases();
        $json = wp_remote_retrieve_body( $rs );

        if ( empty( $json ) ) {
            return [];
        }

        $items = json_decode( $json, true );
        $items = is_array( $items ) ? $items : [];
        $bases = array_map( [$this, 'evolve_base'], $items['bases'] );

        return $bases;
    }

    protected function evolve_base( array $base ) {
        $base['schema'] = $this->fetch_bases_schema( $base['id'] );
        return $base;
    }

    protected function fetch_bases_schema( $id ) {
        $rs = AirtableApi::get_bases_schema( $id );
        $json = wp_remote_retrieve_body( $rs );

        return empty( $json )
            ? []
            : json_decode( $json, true );
    }
}