<?php 
/*
	Plugin Name: JC Submenu
	Plugin URI: http://jamescollings.co.uk/blog/jc-submenu-dynamic-wordpress-menu-plugin/
	Description: Wordpress Submenu Plugin, automatically populate your navigation menus with custom post_types, taxonomies, or child pages. An easy to use plugin created to be a lightweight menu extension.
	Version: 0.2.2
	Author: James Collings
	Author URI: http://www.jamescollings.co.uk
 */
	
$JCSubmenu = new JCSubmenu();

/**
 * JC Submenu Class
 *
 * Core plugin file, load all required classes
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.2.2
 */
class JCSubmenu{
	var $version = '0.2.2';
	var $plugin_dir = false;
	var $plugin_url = false;
	var $prefix = 'jc-submenu';

	/**
	 * Setup plugin
 	 * @return void
	 */
	function __construct(){

		$this->plugin_dir =  plugin_dir_path( __FILE__ );
		$this->plugin_url = plugins_url( '/', __FILE__ );

		add_filter( 'wp_nav_menu_args', array( $this, 'attach_menu_walker' ));

		$this->load_modules();
	}

	/**
	 * Attach custom nav walker
	 *
	 * Hook into theme menu, attach custom walker
	 * 
	 * @param  array $args 
	 * @return array       
	 */
	function attach_menu_walker($args){
		if(empty($args['walker'])){
			$args['walker'] = new JC_Submenu_Nav_Walker();
		}
		return $args;
	}

	/**
	 * Load Required Modules
	 * @return void 
	 */
	function load_modules(){

		include 'SubmenuWalkers.php';
		include 'SubmenuWidgets.php';
		include 'SubmenuModel.php';
		SubmenuModel::init($this);

		include 'SubmenuAdmin.php';
		new SubmenuAdmin($this);
	}
}
?>