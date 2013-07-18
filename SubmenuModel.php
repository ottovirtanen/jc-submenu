<?php 
/**
 * Submenu Model
 *
 * class to interact with the database
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class SubmenuModel{

	/**
	 * Plugin config
	 * @var stdClass
	 */
	static $config;

	/**
	 * Order by options
	 * @var array
	 */
	private static $order_options = array(
		'tax' => array(
			'name' => 'Name',
			'slug' => ' Slug',
			'count' => 'Tax Count',
			'id' => 'ID',
			'none' => 'None',
		),
		'post' => array(
			'title' => 'Post Name',
			'name' => 'Post Slug',
			'none' => 'None',
			'ID' => 'ID',
			'date' => 'Date',
			'comment_count' => 'Total Comments',
			'menu_order' => 'Menu Order'
		)
	);

	/**
	 * Setup class
	 * @param stdClass $config
	 * @return void
	 */
	static function init(&$config){
		self::$config = $config;
	}

	/**
	 * Save Menu item from wp admin navigation page
	 * @param  int $menu_item_id 
	 * @param  array  $args         
	 * @return void
	 */
	static function save_menu_item($menu_item_id = null, $args = array()){
		$type = self::get_post_data($menu_item_id, 'populate-type');
		$value = self::get_post_data($menu_item_id, 'populate-'.$type);

		if($type && $value){

			// validate population type
			if(!in_array($type, array('post','tax', 'page'))){
				return 0;
			}

			if($type == 'post'){
				$post_limit = self::get_post_data($menu_item_id, 'post-limit');
				$post_order = self::get_post_data($menu_item_id, 'post-order');
				$post_orderby = self::get_post_data($menu_item_id, 'post-orderby');
				$post_limit = intval($post_limit);

				// validate order
				if(!array_key_exists($post_orderby, self::get_order_options('post'))){
					echo $post_orderby;
					echo 'A';
					return 0;
				}
				if(!in_array($post_order, array('ASC', 'DESC'))){
					echo 'B';
					return 0;
				}

				self::save_meta($menu_item_id, 'post-limit', $post_limit);
				self::save_meta($menu_item_id, 'post-order', $post_order);
				self::save_meta($menu_item_id, 'post-orderby', $post_orderby);
			}elseif($type == 'tax'){
				$tax_order = self::get_post_data($menu_item_id, 'tax-order');
				$tax_orderby = self::get_post_data($menu_item_id, 'tax-orderby');
				$tax_empty = self::get_post_data($menu_item_id, 'tax-empty');

				$tax_empty = $tax_empty == 1 ? 1 : 0; 

				// validate order
				if(!array_key_exists($tax_orderby, self::get_order_options('tax'))){
					return 0;
				}
				if(!in_array($tax_order, array('ASC', 'DESC'))){
					return 0;
				}

				self::save_meta($menu_item_id, 'tax-order', $tax_order);
				self::save_meta($menu_item_id, 'tax-orderby', $tax_orderby);
				self::save_meta($menu_item_id, 'tax-empty', $tax_empty);
			}

			// all validated save rest
			self::save_meta($menu_item_id, 'populate-type', $type);
			self::save_meta($menu_item_id, 'populate-value', $value);
			self::save_meta($menu_item_id, 'autopopulate', 1);
		}

	}

	/**
	 * Capture menu item $_POST data
	 * @param $menu_item_id
	 * @param $key
	 */
	static function get_post_data($menu_item_id = 0, $key = ''){
		if(isset($_POST[self::$config->prefix.'-'.$key][$menu_item_id]) 
			&& !empty($_POST[self::$config->prefix.'-'.$key][$menu_item_id])){
			return $_POST[self::$config->prefix.'-'.$key][$menu_item_id];
		}else{
			return '';
		};
	}

	/**
	 * Update post meta data
	 * @param int $menu_item_id
	 * @param string $meta_key
	 * @param string $new_value
	 */
	static function save_meta($menu_item_id = 0, $meta_key = '', $new_value = ''){
		$old_value = get_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, true );
		if($old_value == ''){
			// add value
			add_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, $new_value );
		}else{
			// update value
			update_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, $new_value, $old_value );
		}
	}

	/**
	 * Remove menu item settings
	 * @param  int $menu_item_id 
	 * @return void 
	 */
	static function clear_menu_item($menu_item_id = null){
		$keys = array(
			'populate-type',
			'populate-value',
			'autopopulate',
			'post-limit',
			'post-order',
			'post-orderby',
			'tax-order',
			'tax-orderby',
			'tax-empty'
		);
		
		foreach($keys as $meta_key){
			$old_value = get_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, true );
			if($old_value != ''){
				delete_post_meta( $menu_item_id, self::$config->prefix.'-'.$meta_key, $old_value );
			}
		}
	}

	/**
	 * Retrieve orderby options
	 * @param string $type tax | post
	 */
	static function get_order_options($type){
		if($type == 'tax'){
			return self::$order_options['tax'];
		}elseif($type == 'post'){
			return self::$order_options['post'];
		}
	}

	/**
	 * Get plugin meta item
	 * @param  int $menu_item_id 
	 * @param  string $key          
	 * @return string               
	 */
	static function get_meta($menu_item_id, $key){
		return get_post_meta( $menu_item_id, self::$config->prefix.'-'.$key, true );
	}
}
?>