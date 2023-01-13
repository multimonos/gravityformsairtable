<?php

namespace GFAirtable\Airtable;

class AirtableAdmin
{
    const BASEURI = 'https://airtable.com';

    public static function table_url( $baseid, $tableid ) {
        return self::BASEURI . "/{$baseid}/{$tableid}";
    }
}