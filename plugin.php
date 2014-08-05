<?php
/**
 * Plugin Name: Lava Plugin
 * Plugin URI: http://www.anchorwave.com
 * Description: Used to create featured post UI page.
 * Version: 1.0.0
 * Author: Jameel Bokhari
 * Author URI: http://www.anchorwave.com
 * License: GPL2
 */
/*
Copyright 2013  Jameel Bokhari  ( email : me@jameelbokhari.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("ECT_RELATED_CONTENT_PATH", dirname(__FILE__));
define("ECT_RELATED_CONTENT_URL", plugin_dir_url( __FILE__ ) );
require_once('lava/class.lava.plugin.core.php');
/**
 * Class LavaPlugin
 * @uses LavaCorePlugin Version 2.2
 * @package ECT Related Content
 */
class LavaPlugin extends LavaCorePlugin22 {
	public $prefix = 'rc_';
	public $ver = '1.0.0';
	public $option_prefix = 'rc_';
	public $name = 'rc';
	public $localize_object = 'RC';
	protected $plugin_slug;
	protected static $instance;
	protected $templates;
	public function __construct(){
		parent::__construct();
	}
	public function init(){
		$this->useFrontendCss = true;
		$this->useFrontendJs = true;
		$this->useAdminCss = true;
		$this->useAdminJs = true;
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", array($this, 'add_settings_page') );
	}
	
	public static function get_instance() {
		if (null == self::$instance ) {
			self::$instance = new LavaPlugin();
		}
		return self::$instance;
	}
	function option($option, $default = null){
		echo $this->get_option($option, $default);
	}
	function get_option($option, $default = null){
		return $this->get_cache($option, $default);
	}
	/**
	 * Overrides default functionality
	 * @return type
	 */
	function admin_enqueue_scripts_and_styles(){
		$version = $this->get_script_version();
		if ( $this->useFrontendCss ){
			wp_enqueue_style( 'related-content-admincss', $this->cssdir . 'admin.css', array(), $version, $media = 'all' );
		}

		if ( $this->useFrontendJs ){
			wp_register_script( 'related-content-adminjs', $this->jsdir . 'admin.js', 'jquery', $version );

			$js_global = $this->get_localized_js_object_name();
			$adminJSVars = $this->set_frontend_loc_js_values();
			apply_filters( "related-content-admin-js-vars", $adminJSVars );
			wp_localize_script( 
				'related-content-adminjs',
				$js_global,
				$adminJSVars
			);

			wp_enqueue_script('related-content-adminjs');
		}
		wp_enqueue_script( 'suggest' );
		wp_enqueue_script( 'autocomplete', $this->jsdir . 'jquery-ui-autocomplete.min.js', array('jquery'), $version );	
	}
	function add_settings_page($links) { 
	  $settings_link = '<a href="'.$this->static['options_page']['parent_slug'].'?page='.$this->static['options_page']['menu_slug'].'">Settings</a>'; 
	  array_unshift($links, $settings_link); 
	  return $links; 
	}
}
$LavaPlugin = LavaPlugin::get_instance();
add_shortcode('test_sortable', 'test_sortable_query' );
function test_sortable_query($atts, $content = null){
	extract( shortcode_atts(
    	array('id' => '')
	, $atts) );
	$LavaPlugin = LavaPlugin::get_instance();
	$order = $LavaPlugin->lava_options[$id]->get_value();
	$order = explode(',', $order);
	/**
		 * The WordPress Query class.
		 * @link http://codex.wordpress.org/Function_Reference/WP_Query
		 *
		 */
		$args = array(
			"post__in" => $order
		);
	
	$query = new WP_Query( $args );
	
	return print_r($order, true);
}
// echo "<pre>";
// print_r($LavaPlugin);
// echo "</pre>";