<?php

class BBBP_Topic_Notifier_Local extends BBBP_Topic_Notifier {

	public function notify( $users, $topic ) {

		//notify of new topic using bp local notification

		// Get author information
		$topic_author = $topic->post_author;

		// Get some reply information
		$args = array(
			'item_id'          => $topic->ID,
			'component_name'   => 'bbbp_topic_notifier',
			'component_action' => 'bbbp_new_topic',
			'date_notified'    => get_post( $topic )->post_date,
			'secondary_item_id' => $topic_author
		);

		foreach ( $users as $user_id ) {

			//no self notification
			if ( $user_id == $topic_author ) {
				continue;
			}

			$args['user_id'] = $user_id;

			bp_notifications_add_notification( $args );
		}

	}

	public function read( $user_id, $topic ) {
		bp_notifications_mark_notifications_by_item_id( $user_id, $topic->ID,'bbbp_topic_notifier',  'bbbp_new_topic' , false, 0 );
	}

}
