<?php

namespace GFAirtable\Rest\Validator;

class NonceValidator
{
    const ENABLED = true;
    const NONCE_ACTION = 'wp_rest';

    public function is_valid( \WP_REST_Request $request ) {
        if ( ! self::ENABLED ) {
            return true;
        }

        $nonce = $request->get_header( 'x_wp_nonce' );

        if ( is_null( $nonce ) || empty( $nonce ) ) {
            return false;
        }

        return wp_verify_nonce( $nonce, self::NONCE_ACTION );
    }

    public function error() {
        return new \WP_Error(
            'missing_or_invalid_nonce',
            'Missing or invalid nonce',
            ['status' => 401]
        );
    }
}