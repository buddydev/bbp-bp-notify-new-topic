<?php

class BBBP_Topic_Notifier_Component extends BP_Component {

	public function __construct( $id = '', $name = '', $path = '', $params = array() ) {
		parent::start( 'bbbp_topic_notifier', 'BBPress Notifier' );

		buddypress()->active_components[$this->id] = 1;
	}

	public function includes( $includes = array() ) {
		//do nothing
	}

	public function setup_globals( $args = array() ) {

		parent::setup_globals( array(
			'slug'                  => false,
			'has_directory'         => false,
			'notification_callback' => array( $this, 'format' ),
			'global_tables'         => false,
			'meta_tables'           => false
		) );

	}

	/**
	 * Format Notification
	 *
	 * @param $action
	 * @param $item_id
	 * @param $secondary_item_id
	 * @param $total_items
	 * @param string $format
	 *
	 * @return mixed|void
	 */
	public function format( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

		// New topic notifications, not using bbp_new_topic since bbp can implement it in future
		if ( 'bbbp_new_topic' === $action ) {

			$topic_id = $item_id;

			$topic_link  = wp_nonce_url( add_query_arg( array( 'action' => 'bbbp_topic_mark_read', 'topic_id' => $item_id ), bbp_get_topic_url( $topic_id ) ), 'bbbp_mark_topic_' . $topic_id );

			$title_attr   = 'New Topic';

			if ( (int) $total_items > 1 ) {
				$text   = sprintf( __( '%d new topics', 'bbp-bp-notify-new-topic' ), (int) $total_items );
			} else {
				//1 new topic
				$text   = sprintf( __( '%d new topic', 'bbp-bp-notify-new-topic' ), (int) $total_items );
			}

			// WordPress Toolbar
			if ( 'string' === $format ) {
				$return = '<a href="' . esc_url( $topic_link ) . '" title="' . esc_attr( $title_attr ) . '">' . esc_html( $text ) . '</a>';

				// Deprecated BuddyBar
			} else {
				$return = array(
					'text' => $text,
					'link' => $topic_link
				);
			}

			return $return;
		}

	}

}
