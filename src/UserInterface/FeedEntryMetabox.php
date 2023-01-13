<?php

namespace GFAirtable\UserInterface;

use GFAirtable\Models\FeedEntry;

class FeedEntryMetabox
{
    public static function get_html( $addon, $args ) {
        $form = $args['form'];
        $entry = $args['entry'];
        $feeds = $addon->get_feeds( $form['id'] );


        foreach ( $feeds as $feed ) {
            $feed_entry = new FeedEntry( $feed, $entry );
            $record_id = $feed_entry->get_record_id(); // this is not controllable by user
            $was_pushed = $feed_entry->was_pushed();

            // html
            ?>
            <p>
                <strong><span style="margin-right:.5rem;"><?php echo( $was_pushed ? '<span>&check;</span>' : '<span style="color:#f34a5c;">&cross;</span>' ); ?></span></strong>
                <strong><?php echo $feed['meta']['feedName']; ?></strong>

                <?php if ( ! $feed['is_active'] ) : ?>
                    &nbsp;&nbsp;<small style="color:#f34a5c;font-weight: bold;">INACTIVE</small>
                <?php endif; ?>

                <a href="<?php echo $feed_entry->table_url(); ?>"
                   target="_blank"
                    <?php if ( $was_pushed ) : ?>
                        data-airtable-id="<?php echo $record_id; ?>"
                    <?php endif; ?>
                   style="text-decoration: none;"><span style="height:16px;width:16px;" class="dashicons dashicons-database"></span></a>
            </p>
            <?php


//            if ( $was_pushed ) {
//                $html .= '<a data-airtable-id="' . $record_id . '" href="' . $feed_entry->table_url() . '" target="_blank"><span class="dashicons dashicons-database"></span></a>';
//                    . '<li><a href="' . admin_url( 'admin-ajax.php' ) . "?action=" . SyncFeedEntryAction::ID . "&entry_id={$entry['id']}&form_id={$form['id']}&feed_id={$feed['id']}&force=1&r=" . urlencode( $_SERVER["REQUEST_URI"] ) . '">Force Sync</a></li>';

//            } else {
//                $retry_url = admin_url( 'admin-ajax.php' ) . "?action=" . SyncFeedEntryAction::ID . "&entry_id={$entry['id']}&form_id={$form['id']}&feed_id={$feed['id']}&r=" . urlencode( $_SERVER["REQUEST_URI"] );
//                $html .= '<li><a href="' . $retry_url . '">Sync</a></li>';
//            }

        }

//        $html = ob_get_contents();

//        return $html;
    }
}