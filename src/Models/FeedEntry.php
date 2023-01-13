<?php

namespace GFAirtable\Models;

use GFAirtable\Airtable\AirtableAdmin;

class FeedEntry
{
    protected $feed = null;
    protected $entry = null;

    public function __construct( array $feed, array $entry ) {
        $this->feed = $feed;
        $this->entry = $entry;
    }

    public static function feed_metakey( $feed, $name ) {
        return $feed['addon_slug'] . '_' . $feed['id'] . '_' . $name;
    }

    public function metakey( $name ): string {
        // localize to feed via addonName_feedID_name
        return self::feed_metakey( $this->feed, $name );
    }

    protected function get( $name ) {
        return gform_get_meta( $this->entry['id'], $this->metakey( $name ) );
    }

    protected function set( $name, $value ) {
        return gform_update_meta( $this->entry['id'], $this->metakey( $name ), $value );
    }

    public function delete( $name ) {
        return gform_delete_meta( $this->entry['id'], $this->metakey( $name ) );
    }

    public function table_url() {
        return AirtableAdmin::table_url( $this->get_base_id(), $this->get_table_id() );
    }

    public function get_feed() {
        return $this->feed;
    }

    public function get_entry() {
        return $this->entry;
    }

    public function get_base_id() {
        list( $baseid, $tableid ) = explode( '|', $this->feed['meta']['base_table_ids'] );
        return $baseid;
    }

    public function get_table_id() {
        list( $baseid, $tableid ) = explode( '|', $this->feed['meta']['base_table_ids'] );
        return $tableid;
    }

    public function get_record_id() {
        return $this->get( 'record_id' );
    }

    public function set_record_id( $id ) {
        return $this->set( 'record_id', $id );
    }

    public function get_form_id() {
        return $this->feed['form_id'] ?? false;
    }

    public function get_form() {
        $id = $this->feed['form_id'] ?? false;
        $form = \GFAPI::get_form( $id );// $this->feed['form_id']);
        return $form
            ? $form :
            new \WP_Error( 'not_found', "form with id {$id} does not exist" );
    }

    public function was_pushed() {
        $id = $this->get_record_id();
        return ! empty( $id );
    }


}
