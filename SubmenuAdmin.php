<?php
/**
 * Administration Menu Class
 *
 * Add custom options to nav-menu.php
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class SubmenuAdmin{

	/**
	 * Plugin config
	 * @var stdClass
	 */
	private $config = null;

	/**
	 * Setup class
	 * @param stdClass $config
 	 * @return void
	 */
	public function __construct(&$config){
		$this->config = $config;

		// include js/css
		add_action( 'admin_enqueue_scripts', array($this, 'load_scripts'));

		// on menu save
		add_action('wp_update_nav_menu_item', array($this, 'save_nav_menu'), 10, 3);

		add_filter( 'wp_edit_nav_menu_walker', array($this, 'set_edit_walker'));
	}

	/**
	 * Attach plugin assets
	 * @return void
	 */
	public function load_scripts(){
		// attach files
		wp_enqueue_script('jc-submenu-scripts', $this->config->plugin_url .'/assets/js/main.js', array('jquery'), '1.0', true);
		wp_enqueue_style('jc-submenu-admin-css', $this->config->plugin_url .'/assets/css/admin.css');
	}

	/**
	 * Save custom menu item options
	 * @param  int $menu_id         
	 * @param  int $menu_item_db_id 
	 * @param  array $args            
	 * @return void                  
	 */
	public function save_nav_menu($menu_id, $menu_item_db_id, $args){

		if(!isset($_POST['menu-item-title']) || empty($_POST['menu-item-title']))
			return false;

		foreach($_POST['menu-item-title'] as $menu_item_id => $menu_item_title){
			if(isset($_POST[$this->config->prefix.'-autopop']) && array_key_exists($menu_item_id, $_POST[$this->config->prefix.'-autopop'])){
				// save post meta for active items
				SubmenuModel::save_menu_item($menu_item_id);

			}else{
				// clear post meta for inactive items
				SubmenuModel::clear_menu_item($menu_item_id);
			}
		}
	}

	/**
	 * Change nav-menu.php walker
	 * @return  string
	 */
	public function set_edit_walker(){
		return 'JC_Submenu_Admin_Walker';
	}

}