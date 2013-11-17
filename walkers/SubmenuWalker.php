<?php

class JC_Submenu_Nav_Walker extends Walker_Nav_Menu {

	private $hierarchical = true;	// diplsay menu as hierarchy
	private $dynamic_count = 1;		// dynamic population count

	private $menu_start = 0; 	// menu start depth
	private $menu_depth = 0; 	// menu depth

	private $section_menu = false;	// display as menu section
	private $split_menu = false;	// display as split menu
	private $_section_ids = array();
	private $selected_section_ids = array(); 	// current id and all ansestor ids

	public function __construct($args = array()){
		$this->hierarchical = isset($args['hierarchical']) && $args['hierarchical'] == 0 ? 0 : 1;
		$this->split_menu = isset($args['split_menu']) ? true : false;
		$this->section_menu = isset($args['section_menu']) && is_bool($args['section_menu']) ? $args['section_menu'] : false;
			
		// Display Split Menu Section
		$this->menu_start_item = isset($args['menu_item']) ? intval($args['menu_item']) : 0;
		$this->menu_start = isset($args['menu_start']) ? intval($args['menu_start']) : 0;
		$this->menu_depth = isset($args['menu_depth']) ? intval($args['menu_depth']) : 5;
		$this->show_parent = isset($args['show_parent'])  && $args['show_parent'] == 1 ? 1 : 0;
	}

	function start_el( &$output, $item, $depth, $args ) {

		parent::start_el($output, $item, $depth, $args);
	}
 
	function end_el( &$output, $item, $depth = 0, $args = array() ) {

		parent::end_el($output, $item, $depth, $args);
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		if( $this->hierarchical == 1 )
			parent::start_lvl($output, $depth, $args);
	}
 
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		if( $this->hierarchical == 1 )
			parent::end_lvl($output, $depth, $args);
	}

	/**
	 * Display array of elements hierarchically.
	 *
	 * It is a generic function which does not assume any existing order of
	 * elements. max_depth = -1 means flatly display every element. max_depth =
	 * 0 means display all levels. max_depth > 0  specifies the number of
	 * display levels.
	 *
	 * @since 2.1.0
	 *
	 * @param array $elements
	 * @param int $max_depth
	 * @return string
	 */
	function walk( $elements, $max_depth) {

		$args = array_slice(func_get_args(), 2);
		$output = '';

		if ($max_depth < -1) //invalid parameter
			return $output;

		if (empty($elements)) //nothing to walk
			return $output;

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		// flat display
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e )
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			return $output;
		}

		/**
		 * Loop through all menu items checking to see if if any items have been
		 * marked for auto population using this plugin
		 */
		foreach($elements as $k => $e){
			$break = false;
			
			// Hide element for logged in users
			if(!is_user_logged_in() && intval(SubmenuModel::get_meta($e->$id_field, 'admin')) == 1){
				unset($elements[$k]);
				$break = true;
			}

			if(!$break){
				$current_dynamic_parent = false;

				// check to see if auto populate flag has been set
				if(SubmenuModel::get_meta($e->$id_field, 'autopopulate') == 1){
					
					$type = SubmenuModel::get_meta($e->$id_field, 'populate-type');
					$value = SubmenuModel::get_meta($e->$id_field, 'populate-value');

					if($type == 'post'){
						$this->populate_post_items($elements, $e, $value, $current_dynamic_parent);
					}elseif($type == 'page'){
						$this->populate_page_items($elements, $e, $value, $current_dynamic_parent);
					}elseif($type == 'tax'){
						$this->populate_tax_items($elements, $e, $value, $current_dynamic_parent);
					}
				}
			}

			$this->set_menu_item_state($e, false);

			if(in_array($e->$id_field, $this->selected_section_ids)){
				$this->set_parent_ids($elements, $e->$id_field);
			}
		}

		//Set Menu Item Depth 
		$elements = $this->set_elements_depth($elements, 0, true);
	
		if($this->section_menu || $this->split_menu){		

			// process section of menu
			if($this->section_menu == true){
				$new_elems = array();
				$old_elems = $elements;

				foreach($old_elems as $item){
					if(($this->menu_start_item == $item->db_id && $this->show_parent == 1) || $item->menu_item_parent == $this->menu_start_item || in_array($item->menu_item_parent, $this->_section_ids)){

						// set depth start from first item
						if(empty($new_elems)){
							$this->menu_start = $item->menu_depth;

							if($this->show_parent)
								$this->menu_depth++;
						}

						$new_elems[] = $item;	

						if(!in_array($item->db_id, $this->_section_ids)){
							$this->_section_ids[] = $item->db_id;	
						}
					}
				}
			}

			// process split menu
			if($this->split_menu == true){
				
				$new_elems = array();
				$old_elems = $elements;
				$section_parents = array();
				$parent_elem = false;
				$parent_count = 0;

				// get relevent parent id
				if($this->menu_start > 0){
					foreach($old_elems as $elm){
						
						if(in_array($elm->$id_field, $this->selected_section_ids)){

							if($elm->menu_depth == $this->menu_start - 1){
								
								// echo 'Depth ('.$elm->$id_field.'): '.$elm->menu_depth . '/' .$this->menu_start .  '<br />';
								$new_elems[] = $elm;
								$section_parents[] = $elm->$id_field;
							}
						}
					}
				}else{
					$section_parents = array(0);
				}

				if($this->show_parent && $this->menu_start > 0){
					$this->menu_start--;
					$this->menu_depth++;
				}

				while($parent_count < count($section_parents)){

					$parent_count = count($section_parents);

					foreach($old_elems as $elm){

						if(in_array($elm->$parent_field, $section_parents) && !in_array($elm->$id_field, $section_parents)){
							$section_parents[] = $elm->$id_field;
							$new_elems[] = $elm;
						}
					}	
				}
			}

			// process elements to display
			foreach($new_elems as $k => $elm){

				if($elm->menu_depth > $this->menu_start){
					
					if($elm->menu_depth >= ($this->menu_start + $this->menu_depth)){
						// remove items that are too deep
						unset($new_elems[$k]);
					}
				}elseif($elm->menu_depth == $this->menu_start){

					// need to change to parent = 0
					$new_elems[$k]->$parent_field = 0;
				}else{

					// unset elements beneath
					unset($new_elems[$k]);
				}
			}

			$elements = $new_elems;
		}

		// escape if no elements are left
		if(empty($elements)){
			return false;
		}

		/*
		 * need to display in hierarchical order
		 * separate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();

		foreach ( $elements as $e) {

			if ( 0 == $e->$parent_field ){
				$top_level_elements[] = $e;
			}else{
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		/*
		 * when none of the elements is top level
		 * assume the first one must be root of the sub elements
		 */
		if ( empty($top_level_elements) ) {

			$first = array_slice( $elements, 0, 1 );
			$root = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e) {
				if ( $root->$parent_field == $e->$parent_field )
					$top_level_elements[] = $e;
				else
					$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		foreach ( $top_level_elements as $e )
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );

		/*
		 * if we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless
		 */
		if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans )
				foreach( $orphans as $op )
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
		 }

		 return $output;
	}

	public function set_elements_depth($elements, $parent = 0, $menu = false){
		/**
		 * Set Menu Item Depth
		 */
		$menu_depths = array();
		$menu_elements = array();
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];
		$counter = 0;

		$depth_field = $menu ? 'menu_depth' : 'depth';

		while(count($menu_elements) < count($elements) && $counter < 5){

			foreach($elements as $k => $e){
				if($e->$parent_field == $parent){

					if(!isset($menu_depths[0]) || !is_array($menu_depths[0])){
						$menu_depths[0] = array();
					}

					// add id to $menu_elements
					if(!isset($elements[$k]->$depth_field)){
						$menu_elements[] = $e->$id_field;
						$menu_depths[0][] = $e->$id_field;
						$elements[$k]->$depth_field = 0;	
					}

				}else{

					$break = false;
					foreach($menu_depths as $tax_depth => $parents){
						foreach($parents as $parent_id){
							
							if($e->$parent_field == $parent_id){

								if(!isset($menu_depths[$tax_depth+1]) || !is_array($menu_depths[$tax_depth+1])){
									$menu_depths[$tax_depth+1] = array();
								}

								// add id to $menu_elements
								if(!isset($elements[$k]->$depth_field)){
									$menu_elements[] = $e->$id_field;	
									$menu_depths[$tax_depth+1][] = $e->$id_field;
									$elements[$k]->$depth_field = $tax_depth+1;
								}
								
								$break = true;
								continue;
							}
						}

						if($break)
							continue;
					}
				}
			}
			$counter++;
		}

		return $elements;
	}

	/**
	 * Populate menu item with pages
	 * 
	 * @param  array $elements               	list of menu elements
	 * @param  stdObj $e                      	current menu item
	 * @param  string $value                  	chosen page_parent
	 * @param  boolean $current_dynamic_parent	current menu item flag
	 * @return void
	 */
	public function populate_page_items(&$elements, $e, $value, &$current_dynamic_parent){
		global $post;
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];
		$page_elements = array();
		$order = SubmenuModel::get_meta($e->$id_field, 'page-order');
		$orderby = SubmenuModel::get_meta($e->$id_field, 'page-orderby');
		$exclude = SubmenuModel::get_meta($e->$id_field, 'page-exclude');

		$pages = get_pages(array( 
			'hierarchical' => 1, 
			'child_of' => $value,
			'sort_order' => $order,
			'sort_column' => $orderby ,
			'exclude' => $exclude
		));

		foreach($pages as $p){
			
			$p->$id_field = $p->ID;
			$p->title = $p->post_title;
			$p->url = get_permalink( $p->ID);

			if($p->post_parent == $value){
				$p->$parent_field = $e->$id_field;
			}else{
				$p->$parent_field = $p->post_parent;
			}

			// check if this page is the current page
			if( is_page($p->ID) && $post->ID == $p->ID){
				$current_dynamic_parent = $p->$parent_field;
				$p->classes = array('current-menu-item');
				$p->split_section = true;
			}
			
			$page_elements[] = clone($p);
		}

		if($current_dynamic_parent){
			$this->child_page_walker($page_elements, $current_dynamic_parent);
		}

		$elements = array_merge($elements, $page_elements);

		$this->set_menu_item_state($e, $current_dynamic_parent);
	}

	/**
	 * Populate menu item with a post type
	 * 
	 * @param  array $elements               	list of menu elements
	 * @param  stdObj $e                      	current menu item
	 * @param  string $value                  	chosen post type
	 * @param  boolean $current_dynamic_parent	current menu item flag
	 * @return void
	 */
	public function populate_post_items(&$elements, $e, $value, &$current_dynamic_parent){
		global $post;
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		$limit = SubmenuModel::get_meta($e->$id_field, 'post-limit');
		$order = SubmenuModel::get_meta($e->$id_field, 'post-order');
		$orderby = SubmenuModel::get_meta($e->$id_field, 'post-orderby');
		$post_tax = SubmenuModel::get_meta($e->$id_field, 'post-tax');
		$post_term = intval(SubmenuModel::get_meta($e->$id_field, 'post-term'));

		$post_query = array(
			'post_type' => $value, 
			'posts_per_page' => $limit,
			'order' => $order,
			'order_by' => $orderby
		);

		// add taxonomy filter
		if( !empty($post_tax) && taxonomy_exists( $post_tax ) ){
			$tax_args = array( 'taxonomy' => $post_tax, 'field' => 'id' );

			if(get_term_by( 'id', $post_term, $post_tax)){
				$tax_args['terms'] = $post_term;
			}

			$post_query['tax_query'] = array(
				$tax_args
			);
		}

		// run post type query
		$post_type_query = new WP_Query($post_query);

		if($post_type_query->have_posts()){
			foreach($post_type_query->posts as $p){
				
				// set menu item variables
				$p->$id_field = $p->ID;
				$p->title = $p->post_title;
				$p->url = get_permalink( $p->ID);
				$p->$parent_field = $e->$id_field;
				
				// check if post item is the current page
				if(is_single($post) && is_singular( $value ) && $post->ID == $p->ID){
					$current_dynamic_parent = $p->$parent_field;
					$p->classes = array('current-menu-item');
					$p->split_section = true;
				}
				
				$elements[] = clone($p);
			}
		}
		
		if($current_dynamic_parent){
			$this->child_page_walker($elements, $current_dynamic_parent);
		}

		$this->set_menu_item_state($e, $current_dynamic_parent);
	}

	public function populate_tax_items(&$elements, $e, $value, &$current_dynamic_parent){

		global $post;
		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		$dynamic_item_prefix = str_repeat(0, $this->dynamic_count);

		$tax_parent_id = $e->$id_field;
		
		$order = SubmenuModel::get_meta($tax_parent_id, 'tax-order');
		$orderby = SubmenuModel::get_meta($tax_parent_id, 'tax-orderby');
		$hide = SubmenuModel::get_meta($tax_parent_id, 'tax-empty');
		$tax_max_depth = intval(SubmenuModel::get_meta($tax_parent_id, 'tax-depth'));

		$terms = get_terms( $value, array(
			'hide_empty' => $hide,
			'order' => $order,
			'order_by' => $orderby
		) );

		$tax_elements = array();


		foreach($terms as $t){
			$t->$id_field = $dynamic_item_prefix . $t->term_id;
			$t->ID = $t->term_id;
			$t->title = $t->name;
			$t->url = get_term_link( $t, $value );
			
			if($t->parent == 0){
				$t->$parent_field = $tax_parent_id;
				$t->test = $tax_parent_id;
			}else{
				$t->$parent_field = $dynamic_item_prefix . $t->parent;
			}

			if((is_category() && is_category( $t->ID )) || (is_tag() && is_tag( $t->slug )) || is_tax( $value, $t->slug ) || ( is_singular() && has_term( $t->term_id, $value ) ) ){
				$current_dynamic_parent = $t->$parent_field;
				$t->classes = array('current-menu-item');
				$t->split_section = true;
			}
			
			$tax_elements[] = clone($t);	
		}

		// term depth
		if($tax_max_depth > 0){
			$tax_elements = $this->set_elements_depth($tax_elements, $tax_parent_id);
			
			foreach($tax_elements as $tag_key => $tag_elem){
				
				if($tag_elem->depth >= $tax_max_depth){
					unset($tax_elements[$tag_key]);
				}
			}
		}

		$elements = array_merge($elements, $tax_elements);

		if($current_dynamic_parent){
			$this->child_page_walker($elements, $current_dynamic_parent);
		}

		$this->dynamic_count++;

		$this->set_menu_item_state($e, $current_dynamic_parent);
	}

	public function set_menu_item_state($item, $current_dynamic_parent){

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		if($item->current == 1 || $item->current_item_ancestor == 1 || $item->current_item_parent){

			if(!in_array($item->$id_field, $this->selected_section_ids)){
				$this->selected_section_ids[] = $item->$id_field;
			}

			$item->split_section = true;
		}
	
		if($current_dynamic_parent){
			$item->classes[] = 'current-menu-ancestor';

			if(!in_array($item->$id_field, $this->selected_section_ids)){
				$this->selected_section_ids[] = $item->$id_field;
			}

			$item->split_section = true;
		}
	}

	/**
	 * Loop through all parent elements until 0
	 *
	 * Add split section its to selected_section_ids array()
	 * 
	 * @param array $elements 
	 * @param int $parent   
	 */
	public function set_parent_ids($elements, $parent){

		$parent_field = $this->db_fields['parent'];
		$id_field = $this->db_fields['id'];

		if($parent == 0)
			return;

		foreach($elements as $item){
			if($item->$id_field == $parent){
				if($item->$parent_field > 0){
					$this->selected_section_ids[] = $item->$parent_field;	
					$this->set_parent_ids($elements, $item->$parent_field);
					return;
				}
			}
		}

	}

	public function child_page_walker(&$elements, $parent){

		$parent_field = $this->db_fields['parent'];
		$id_field = $this->db_fields['id'];

		foreach($elements as &$p){

			if(strval($p->$id_field) === strval($parent)){
				$p->classes = array('current-menu-ancestor');
				$p->split_section = true;
				$this->child_page_walker($elements, strval($p->$parent_field));
				return;
			}
			
		}
	}
}