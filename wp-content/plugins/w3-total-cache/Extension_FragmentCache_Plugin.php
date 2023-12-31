<?php
namespace W3TC;

/**
 * W3 FragmentCache plugin
 */
class Extension_FragmentCache_Plugin {
	private $_config = null;
	private $_core = null;

	function __construct() {
		$this->_config = Dispatcher::config();
		$this->_core = Dispatcher::component( 'Extension_FragmentCache_Core' );
	}

	/**
	 * Runs plugin
	 */
	function run() {
		add_filter( 'w3tc_config_default_values', array( $this, 'w3tc_config_default_values' ) );

		/**
		 * This filter is documented in Generic_AdminActions_Default.php under the read_request method.
		*/
		add_filter( 'w3tc_config_key_descriptor', array( $this, 'w3tc_config_key_descriptor' ), 10, 2 );

		$is_active = $this->_config->is_extension_active_frontend( 'fragmentcache' );
		$engine    = $this->_config->get_string( array( 'fragmentcache', 'engine' ) );

		// remainder only when extension is frontend-active.
		if ( ! $is_active || empty( $engine ) ) {
			return;
		}

		add_action( 'init', array( $this, 'on_init' ), 9999999 );

		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		add_filter( 'w3tc_footer_comment', array( $this, 'w3tc_footer_comment' ) );

		if ( 'file' === $engine ) {
			add_action( 'w3_fragmentcache_cleanup', array( $this, 'cleanup' ) );
		}

		add_action( 'switch_blog', array( $this, 'switch_blog' ), 0, 2 );

		$groups = $this->_config->get_array( array( 'fragmentcache', 'groups' ) );
		foreach ( $groups as $group ) {
			$split   = explode( ',', $group );
			$group   = array_shift( $split );
			$actions = $split;
			$this->_core->register_group( $group, $actions, $this->_config->get_integer( array( 'fragmentcache', 'lifetime' ) ) );
		}

		// handle transients by own cache.
		if ( Util_Environment::is_w3tc_pro( $this->_config ) ) {
			$wp_cache = Dispatcher::component( 'ObjectCache_WpObjectCache' );
			$fc_cache = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
			$wp_cache->register_cache( $fc_cache, array( 'transient', 'site-transient' ) );
		}

		// flush operations.
		add_action( 'w3tc_flush_all', array( $this, 'w3tc_flush_all' ), 300 );
		add_action( 'w3tc_flush_fragmentcache', array( $this, 'w3tc_flush_fragmentcache' ) );
		add_action( 'w3tc_flush_fragmentcache_group', array( $this, 'w3tc_flush_fragmentcache_group' ), 10, 2 );

		// usage statistics handling.
		add_action( 'w3tc_usage_statistics_of_request', array( $this, 'w3tc_usage_statistics_of_request' ), 10, 1 );
		add_filter( 'w3tc_usage_statistics_metrics', array( $this, 'w3tc_usage_statistics_metrics' ) );
	}



	public function w3tc_config_default_values( $default_values ) {
		$default_values['fragmentcache'] = array(
			'file.gc' => 3600,
			'memcached.servers' => array( '127.0.0.1:11211' ),
			'memcached.persistent' => true,
			'redis.persistent' => true,
			'redis.servers' => array( '127.0.0.1:6379' ),
			'redis.verify_tls_certificates' => true,
			'lifetime' => 180
		);

		return $default_values;
	}



	function w3tc_flush_all() {
		$cache = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$cache->flush();
	}



	function w3tc_flush_fragmentcache() {
		$cache = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$cache->flush();
	}



	/**
	 * Cleans fragment cache
	 */
	function w3tc_flush_fragmentcache_group( $group, $global = false ) {
		$cache = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$cache->flush_group( $group, $global );
	}



	/**
	 * Does disk cache cleanup
	 *
	 * @return void
	 */
	function cleanup() {
		$this->_core->cleanup();
	}



	/**
	 * Cron schedules filter
	 *
	 * @param array   $schedules
	 * @return array
	 */
	function cron_schedules( $schedules ) {
		$gc_interval = $this->_config->get_integer( array( 'fragmentcache', 'file.gc' ) );

		return array_merge( $schedules, array(
				'w3_fragmentcache_cleanup' => array(
					'interval' => $gc_interval,
					'display' => sprintf( '[W3TC] Fragment Cache file GC (every %d seconds)', $gc_interval )
				),
			) );
	}



	/**
	 * Register actions on init
	 */
	function on_init() {
		do_action( 'w3tc_register_fragment_groups' );
		$actions = $this->_core->get_registered_actions();
		foreach ( $actions as $action => $groups ) {
			add_action( $action, array( $this, 'on_action' ), 0, 0 );
		}
	}



	/**
	 * Flush action
	 */
	function on_action() {
		$w3_fragmentcache = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$actions = $this->_core->get_registered_actions();
		$action = current_filter();
		$groups = $actions[$action];
		foreach ( $groups as $group ) {
			$w3_fragmentcache->flush_group( $group );
		}
	}



	/**
	 * Switch blog action
	 */
	function switch_blog( $blog_id, $previous_blog_id ) {
		$o = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$o->switch_blog( $blog_id );
	}



	public function w3tc_footer_comment( $strings ) {
		$o = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$strings = $o->w3tc_footer_comment( $strings );

		return $strings;
	}



	public function w3tc_usage_statistics_of_request( $storage ) {
		$o = Dispatcher::component( 'Extension_FragmentCache_WpObjectCache' );
		$o->w3tc_usage_statistics_of_request( $storage );
	}



	public function w3tc_usage_statistics_metrics( $metrics ) {
		return array_merge( $metrics, array(
				'fragmentcache_calls_total', 'fragmentcache_calls_hits' ) );
	}

	/**
	 * Specify config key typing for fields that need it.
	 *
	 * @since 2.4.2
	 *
	 * @param mixed $descriptor Descriptor.
	 * @param mixed $key Compound key array.
	 *
	 * @return array
	 */
	public function w3tc_config_key_descriptor( $descriptor, $key ) {
		if ( is_array( $key ) && 'fragmentcache.groups' === implode( '.', $key ) ) {
			$descriptor = array( 'type' => 'array' );
		}

		return $descriptor;
	}
}



$p = new Extension_FragmentCache_Plugin();
$p->run();

if ( is_admin() ) {
	$p = new Extension_FragmentCache_Plugin_Admin();
	$p->run();
}

include W3TC_DIR . '/Extension_FragmentCache_Api.php';
