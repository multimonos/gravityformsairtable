<?php

namespace GFAirtable\Rest\Endpoint;

interface Endpoint
{
    public function handle( \WP_REST_Request $request );
}