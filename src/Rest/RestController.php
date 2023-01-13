<?php

namespace GFAirtable\Rest;

use GFAirtable\Rest\Endpoint\EntrySync;
use GFAirtable\Rest\Endpoint\Feed;
use GFAirtable\Rest\Endpoint\FeedEntryMeta;
use GFAirtable\Rest\Endpoint\FeedReset;
use GFAirtable\Rest\Endpoint\FeedSync;
use GFAirtable\Rest\Endpoint\Ping;

class RestController extends \WP_REST_Controller
{
    public function __construct() {
        $this->namespace = 'gfairtable/v1';
    }

    public function register_routes() {

        register_rest_route( $this->namespace, '/ping', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [new Ping(), 'handle'],
                'permission_callback' => '__return_true',
            ],
        ] );

        register_rest_route( $this->namespace, '/feeds/(?P<feed_id>\d+)/entries/(?P<entry_id>\d+)/meta', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [new FeedEntryMeta(), 'handle'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'feed_id'  => [
                        'required'    => true,
                        'description' => 'gravity forms feed id',
                        'type'        => 'integer',
                    ],
                    'entry_id' => [
                        'required'    => true,
                        'description' => 'gravity forms entry id',
                        'type'        => 'integer',
                    ],
                ]
            ],
        ] );

        register_rest_route( $this->namespace, 'feeds/(?P<feed_id>\d+)/entries/(?P<entry_id>\d+)/sync', [
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [new EntrySync(), 'handle'],
                'permission_callback' => [$this, 'is_admin_permissions_check'],
                'args'                => [
                    'feed_id'  => [
                        'required'    => true,
                        'description' => 'gravity forms feed id',
                        'type'        => 'integer',
                    ],
                    'entry_id' => [
                        'required'    => true,
                        'description' => 'gravity forms entry id',
                        'type'        => 'integer',
                    ],
                ]
            ],
        ] );

        register_rest_route( $this->namespace, '/feeds/(?P<feed_id>\d+)/sync', [
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [new FeedSync(), 'handle'],
                'permission_callback' => [$this, 'is_admin_permissions_check'],
                'args'                => [
                    'feed_id' => [
                        'required'    => true,
                        'description' => 'gravity forms feed id',
                        'type'        => 'integer',
                    ]
                ]
            ],
        ] );

        register_rest_route( $this->namespace, '/feeds/(?P<feed_id>\d+)/reset', [
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [new FeedReset(), 'handle'],
                'permission_callback' => [$this, 'is_admin_permissions_check'],
                'args'                => [
                    'feed_id' => [
                        'required'    => true,
                        'description' => 'gravity forms feed id',
                        'type'        => 'integer',
                    ]
                ]
            ],
        ] );

        register_rest_route( $this->namespace, '/feeds/(?P<feed_id>\d+)', [
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [new Feed(), 'handle'],
                'permission_callback' => [$this, 'is_admin_permissions_check'],
                'args'                => [
                    'feed_id' => [
                        'required'    => true,
                        'description' => 'gravity forms feed id',
                        'type'        => 'integer',
                    ]
                ]
            ],
        ] );

    }

    function is_admin_permissions_check() {
        return true;
        if ( ! current_user_can( 'administrator' ) ) {
            return new \WP_Error( 'rest_forbidden', 'Forbidden', array('status' => 401) );
        }

        return true;
    }


}