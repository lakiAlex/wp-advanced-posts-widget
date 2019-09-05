<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/lakiAlex/
 * @since      1.0.0
 *
 * @package    Wpapw
 * @subpackage Wpapw/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wpapw
 * @subpackage Wpapw/public
 * @author     Lazar Momcilovic <lakialekscs@gmail.com>
 */
class Wpapw_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 *
	 * An instance of this following classes should be passed to the run() function
	 * defined in Wpapw_Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The Wpapw_Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * classes.
	 */

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../dist/css/style.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../dist/js/main.min.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Register the main widget
	 *
	 * @since  1.0.0
	 */
	public function wpapw_widget() {
		register_widget( 'wpapw_widget' );
	}
	
	/**
	 * Track and set the views of each post.
	 *
	 * @since    1.0.4
	 */
	public function wpapw_views() {
	
	    if ( ! is_single() ) return;
	    
	    if ( empty( $postID ) ) {
	        global $post;
	        $postID = $post->ID;    
	    }
	    
	    $count_key = 'post_views_count';
	    $count = get_post_meta( $postID, $count_key, true );
	    if ( $count == '' ) {
	        $count = 0;
	        delete_post_meta( $postID, $count_key );
	        add_post_meta( $postID, $count_key, '0' );
	    } else {
	        $count++;
	        update_post_meta( $postID, $count_key, $count );
	    }
	    
	}

}
