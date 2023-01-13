<?php

namespace GFAirtable\UserInterface;

use GFAirtable\Airtable\Models\AirtableMeta;

class FeedSettings
{
    protected function table_choices() {
        $meta = AirtableMeta::get();

        $blank = ['label' => 'Select table', 'value' => ''];

        $choices = array_reduce( $meta->bases(), function( $list, $base ) {
            $tables = $base['schema']['tables'] ?? [];

            $list = array_merge( $list, array_map( fn( $x ) => [
                'label' => $base['name'] . ' / ' . $x['name'],
                'value' => $base['id'] . '|' . $x['id']
            ], $tables ) );

            return $list;
        }, [$blank] );

        return $choices;
    }

    protected function table_fieldmap_choices( $baseid, $tableid ) {
        $table = AirtableMeta::get()->base_table( $baseid, $tableid );

        if ( $table === false ) return [];

        $fields = $table['fields'];

        return array_map( fn( $x ) => [
            'label' => $x['name'],
            'value' => $x['id']
        ], $fields );
    }

    public function fields() {
        $panels = [];

        $panels [] = [
            'title'  => 'Airtable Feed Settings',
            'fields' => [
                [
                    'label'   => 'Feed Name',
                    'type'    => 'text',
                    'name'    => 'feedName',
                    'class'   => 'small',
                    'tooltip' => sprintf(
                        '<h6>%s</h6>%s',
                        esc_html__( 'Name', 'gravityformsairtable' ),
                        esc_html__( 'Enter a feed name to uniquely identify this setup.', 'gravityformsairtable' )
                    ),
                ], [
                    'label'   => 'Table',
                    'type'    => 'select',
                    'name'    => 'base_table_ids', // baseID + tableID concat with "|"
                    'class'   => 'small',
                    'choices' => $this->table_choices(),
                ]
            ]
        ];


        //field map
        $feed = \GFAPI::get_feed( $_REQUEST['fid'] ?? false );
        $base_table_ids = $feed['meta']['base_table_ids'] ?? null;

        $fieldmap = [];

        // add remote airtable choices when applicable
        if ( ! empty( $base_table_ids ) ) {
            list( $baseid, $tableid ) = explode( '|', $base_table_ids );
            $fieldmap = $this->table_fieldmap_choices( $baseid, $tableid );
        }

        $panels[] = [
            'title'  => esc_html__( 'Map Fields', 'airtableaddon' ),
            'fields' => [
                [
                    'name'              => 'fieldmap',
                    'type'              => 'dynamic_field_map',
                    'enable_custom_key' => false,
                    'tooltip'           => '<h6>' . esc_html__( 'Field Mapping', 'gravityformsairtable' ) . '</h6>' . esc_html__( 'Define the source and target fields to send to Airtable.', 'gravityformsairtable' ),
                    'field_map'         => $fieldmap,
                ],
            ],
        ];

        return $panels;
    }
}
