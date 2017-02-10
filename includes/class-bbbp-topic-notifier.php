<?php

abstract class BBBP_Topic_Notifier {

	public abstract function notify( $users, $topic );
	public function read( $user_id,  $topic ) {
		//a do nothing function,
		//A notifier may implement some action to mark notification as read.
		//delete the notifications etc
	}

	public function delete( $user_id, $topic ) {
		//implement it to act on the delection of the topic
	}

}
