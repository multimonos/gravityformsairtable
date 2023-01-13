<?php

namespace GFAirtable;

use GFAirtable\Action\SyncFeedEntriesAction;
use GFAirtable\Rest\RestController;
use GFAirtable\UserInterface\FeedEntryMetabox;
use GFAirtable\UserInterface\FeedList;
use GFAirtable\UserInterface\FeedSettings;

class AirtableAddon extends \GFFeedAddOn
{
    private static $instance = null;

    protected $_version = '1.0.1';
    protected $_min_gravityforms_version = '1.9.16';
    protected $_slug = 'gravityformsairtable';
    protected $_path = 'gravityformsairtable/airtable.php';
    protected $_title = 'Gravity Forms Airtable Add-On';
    protected $_short_title = 'Airtable';
    protected $_multiple_feeds = true;

    public static function get_instance() {

        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init() {
        $this->_full_path = dirname( __FILE__ );

        parent::init();
        add_filter( 'gform_entry_detail_meta_boxes', [$this, 'register_meta_box'], 10, 3 );
        add_action( 'rest_api_init', [$this, 'rest_api_init'] );
    }

    public function rest_api_init() {
        $controller = new RestController();
        $controller->register_routes();
    }

    public function note_avatar() {
        return $this->get_base_url() . '/static/avatar_48x48.png';
    }

    public function plugin_settings_fields() {
        return [
            [
                'title'  => esc_html__( 'Airtable Settings', 'gravityformsairtable' ),
                'fields' => [
                    [
                        'name'  => 'api_key',
                        'label' => esc_html__( 'Airtable API Key', 'gravityformsairtable' ),
                        'type'  => 'text',
                        'class' => 'small',
                    ],
                ],
            ],
        ];
    }

    public function feed_list_page( $form = null ) {
        echo FeedList::svelte_html();
        parent::feed_list_page( $form );
    }

    public function get_action_links() {
        $links = parent::get_action_links();
        $links = FeedList::add_action_links( $links );
        return $links;
    }

    public function feed_list_columns() {
        return FeedList::get_columns();
    }

    public function get_column_value_sync_status( $feed ) {
        return FeedList::get_column_value_sync_status( $feed );
    }

    public function feed_settings_fields() {
        $feed_settings = new FeedSettings();
        return $feed_settings->fields();
    }

    public function process_feed( $feed, $entry, $form ) {

        if ( ! $feed['is_active'] ) {
            $this->add_feed_error( 'cannot sync entry when feed is inactive', $feed, $entry, $form );
            return new \WP_Error( 'feed_inactive', 'Refused to sync entry while feed is inactive', $feed );
            return $entry;
        }

        $action = new SyncFeedEntriesAction();
        $rs = $action->sync( $form, $feed, [$entry] );

        return is_wp_error( $rs ) ? false : true;
    }

    public function register_meta_box( $meta_boxes, $entry, $form ) {
        $meta_boxes[$this->_slug] = array(
            'title'    => "<img src='{$this->note_avatar()}' height='24' width='24'/>&nbsp; Airtable Feeds",
            'callback' => array($this, 'add_details_meta_box'),
            'context'  => 'side',
        );

        return $meta_boxes;
    }

    public function add_details_meta_box( $args ) {
        echo FeedEntryMetabox::get_html( $this, $args );
    }

    public function scripts() {
        $scripts = array(
            array(
                'handle'    => 'gfAirtable',
                'src'       => $this->get_base_url() . '/app/public/build/bundle.js',
                'version'   => $this->_version,
                'in_footer' => true,
                'callback'  => array($this, 'localize_scripts'),
                'strings'   => array(
                    'siteUrl'   => get_site_url(),
                    'avatarUrl' => $this->note_avatar(),
                    'nonce'     => wp_create_nonce( 'wp_rest' )
                ),
                'enqueue'   => array(array('query' => 'page=gf_edit_forms&view=settings&subview=' . $this->_slug . '&id=_notempty_&fid=_empty_'))
            ),
        );

        return array_merge( parent::scripts(), $scripts );
    }

    public function styles() {
        $styles = array(
            array(
                'handle'  => 'gfAirtable',
                'src'     => $this->get_base_url() . '/app/public/build/bundle.css',
                'version' => $this->_version,
                'enqueue' => array(array('query' => 'page=gf_edit_forms&view=settings&subview=' . $this->_slug . '&id=_notempty_&fid=_empty_'))
            )
        );

        return array_merge( parent::styles(), $styles );
    }


}