<?php

namespace GFAirtable\Rest\Endpoint;

class Ping implements Endpoint
{
    public function handle( \WP_REST_Request $request ) {
        $data = 'PONG';
        return new \WP_REST_Response( $data, 200 );
    }
}