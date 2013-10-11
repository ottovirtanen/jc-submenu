<?php 
/*
	Plugin Name: JC Submenu
	Plugin URI: http://jamescollings.co.uk/blog/jc-submenu-dynamic-wordpress-menu-plugin/
	Description: Wordpress Submenu Plugin, automatically populate your navigation menus with custom post_types, taxonomies, or child pages. An easy to use plugin created to be a lightweight menu extension.
	Version: 0.4
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
 * @version 0.4
 */
class JCSubmenu{
	var $version = '0.4';
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

		// add plugin hooks
		add_action('jcs/menu_section', array($this, 'output_menu_section'), 10, 2);
		add_action('jcs/split_menu', array($this, 'output_split_menu'), 10, 2);
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

	/**
	 * Output menu section
	 *
	 * Display a section of the selected menu in your theme
	 * 
	 * @param  string $menu
	 * @param  array  $args
	 * @return void
	 */
	function output_menu_section($menu, $args = array()){

		$debug = isset($args['debug']) ? $args['debug'] : false;
		$start = isset($args['start']) ? $args['start'] : 0;
		$depth = isset($args['depth']) ? $args['depth'] : 5;
		$show_parent = isset($args['show_parent']) ? $args['show_parent'] : 1;
		
		$options = array('menu' => $menu, 'walker' => new JC_Submenu_Nav_Walker(array(
			'debug' => $debug, 
			'section_menu' => true, 
			'menu_item' => $start, 
			'menu_depth' => $depth, 
			'show_parent' => $show_parent
			))
		);

		if(isset($args['menu_class']))
			$options['menu_class'] = $args['menu_class'];

		if(isset($args['menu_id']))
			$options['menu_id'] = $args['menu_id'];

		if(isset($args['container']))
			$options['container'] = $args['container'];

		if(isset($args['container_id']))
			$options['container_id'] = $args['container_id'];

		if(isset($args['container_class']))
			$options['container_class'] = $args['container_class'];

		wp_nav_menu($options);
	}

	/**
	 * Output Split Menu
	 *
	 * Display a dynamic section of the selected menu in your theme relative to your current page
	 * 
	 * @param  string $menu
	 * @param  array  $args
	 * @return void
	 */
	function output_split_menu($menu, $args = array()){

		$hierarchical = isset($args['menu_hierarchy']) ? $args['menu_hierarchy'] : 1;
		$menu_start = isset($args['menu_start']) ? $args['menu_start'] : 0;
		$menu_depth = isset($args['menu_depth']) ? $args['menu_depth'] : 5;
		$show_parent = isset($args['show_parent']) ? $args['show_parent'] : 1;

		$options = array(
			'menu' => $menu, 'walker' => new JC_Submenu_Nav_Walker(array(
			'hierarchical' => $hierarchical,
			'menu_start' => $menu_start,
			'menu_depth' => $menu_depth,
			'show_parent' => $show_parent,
			'split_menu' => true
			))
		);

		if(isset($args['menu_class']))
			$options['menu_class'] = $args['menu_class'];

		if(isset($args['menu_id']))
			$options['menu_id'] = $args['menu_id'];

		if(isset($args['container']))
			$options['container'] = $args['container'];

		if(isset($args['container_id']))
			$options['container_id'] = $args['container_id'];

		if(isset($args['container_class']))
			$options['container_class'] = $args['container_class'];

		wp_nav_menu($options);
	}
}
?>