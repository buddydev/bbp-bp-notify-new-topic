<?php
/**
 * Plugin Name: bbPress BuddyPress Notify New Topic
 * Version: 1.0.0
 * Author: BuddyDev
 * Description: Notify forum subscribers about new topic using the. It is not a finished product. Needs cleaning up for deleting etc.
 */

class BBBP_Topic_Action_Handler {
	/**
	 * @var BBBP_Topic_Notifier[]
	 */
	private $notifiers = array();

	/**
	 * Register a Notifier
	 *
	 * @param string $notifier_type
	 * @param BBBP_Topic_Notifier $notifier
	 */
	public function register_notifier( $notifier_type, BBBP_Topic_Notifier $notifier ) {
		$this->notifiers[ $notifier_type ] = $notifier;
	}

	/**
	 * Unregister an existing notifier
	 * @param string $notifier_type
	 */
	public function unregister_notifier( $notifier_type ) {
		unset( $this->notifiers[ $notifier_type ] );
	}

	/**
	 * @param $notifier_type
	 *
	 * @return BBBP_Topic_Notifier|null
	 */
	public function get_notifier( $notifier_type ) {

		if ( isset( $this->notifiers[ $notifier_type ] ) ) {
			return $this->notifiers[ $notifier_type ];
		}

		return null;
	}
	/**
	 * Notify forums subscribers on new topic in this forum
	 *
	 * @param int $topic_id
	 */
	public function on_new_topic( $topic_id = 0, $forum_id = 0 ) {

		$topic = bbp_get_topic( $topic_id );
		$forum_id = bbp_get_forum_id( $forum_id );

		$subscribers = bbp_get_forum_subscribers( $forum_id );

		foreach ( $this->notifiers as $notifier ) {
			$notifier->notify( $subscribers, $topic );
		}

	}

	/**
	 * Let the notifier know that the topic was viewed?
	 *
	 */
	public function on_view_topic( ) {

		if ( ! is_user_logged_in() || ! isset( $_GET['action'] ) ||  $_GET['action'] != 'bbbp_topic_mark_read'  ) {
			return ;
		}

		//is single topic?
		if ( ! bbp_is_single_topic() ) {
			return ;
		}

		$topic_id = bbp_get_topic_id();

		$topic = bbp_get_topic( $topic_id );
		$user_id = get_current_user_id();

		foreach ( $this->notifiers as $notifier ) {
			$notifier->read( $user_id, $topic );
		}
	}

	public function load() {
		$path = plugin_dir_path( __FILE__ );

		$files = array(
			'includes/class-bbbp-topic-notifier.php',
			'includes/class-bbbp-local-notifier.php',
			'includes/class-bbbp-notifier-component.php',
		);

		foreach ( $files as $file ) {
			require_once $path . $file;
		}
	}

	public function setup() {

		$this->register_notifier( 'local-bp', new BBBP_Topic_Notifier_Local() );

		add_action( 'bbp_new_topic', array( $this, 'on_new_topic' ), 100, 2 );
		//add_action( 'bbp_new_topic', array( $this, 'on_new_topic' ), 100, 2 );

		add_action( 'bbp_template_redirect', array( $this, 'on_view_topic' ) );
	}
}

//Load/
function bbbp_bp_notifier_setup() {
	$helper = new BBBP_Topic_Action_Handler();
	$helper->load();
	$helper->setup();
}

add_action( 'bp_loaded', 'bbbp_bp_notifier_setup', 0 );

function bbbp_topic_notifier_setup_component() {
	buddypress()->bbbp_topic_notifier = new BBBP_Topic_Notifier_Component();
}

add_action( 'bp_setup_components', 'bbbp_topic_notifier_setup_component', 6 );