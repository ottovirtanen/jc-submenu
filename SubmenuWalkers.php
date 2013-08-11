<?php 

class JC_Submenu_Admin_Walker extends Walker_Nav_Menu {
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl(&$output) {}

	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl(&$output) {
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)'), $item->title );
		}

		$title = empty( $item->label ) ? $title : $item->label;

		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><?php echo esc_html( $title ); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id; ?>">
							<?php _e( 'URL' ); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Navigation Label' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
						<?php _e( 'Title Attribute' ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo $item_id; ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php _e( 'Open link in a new window/tab' ); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
						<?php _e( 'CSS Classes (optional)' ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
						<?php _e( 'Link Relationship (XFN)' ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo $item_id; ?>">
						<?php _e( 'Description' ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
					</label>
				</p>

				<?php require plugin_dir_path( __FILE__ ) . '/views/edit.php'; ?>

				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

}

class JC_Submenu_Nav_Walker extends Walker_Nav_Menu {

	private $menu_item = null;
	private $menu_item_depth = -1;
	private $hierarchical = true;

	private $output = true;

	public function __construct($args = array()){
		$this->menu_item = isset($args['menu_item']) && !empty($args['menu_item']) ? intval($args['menu_item']) : null;
		if($this->menu_item > 0){
			$this->output = false;
		}

		$this->hierarchical = isset($args['hierarchical']) && $args['hierarchical'] == 0 ? 0 : 1;
	}

	function start_el( &$output, $item, $depth, $args ) {

		if($this->menu_item == $item->ID){
			$this->output = true;
			$this->menu_item_depth = $depth;
		}
 		
 		if($this->output && $item->ID != $this->menu_item){
			parent::start_el($output, $item, $depth, $args);
		}
	}
 
	function end_el( &$output, $item, $depth = 0, $args = array() ) {

		if($depth <= $this->menu_item_depth)
			$this->output = false;

		if($this->output && $item->ID != $this->menu_item)
			parent::end_el($output, $item, $depth, $args);
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		if($this->hierarchical == 1)
			parent::start_lvl($output, $depth, $args);
	}
 
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		if($this->hierarchical == 1)
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

		global $post;

		$hidden_elements = array();
		$output_elements = array();
		$dynamic_count = 1;

		foreach($elements as $k => $e){
			$break = false;
			
			if(!is_user_logged_in() && intval(SubmenuModel::get_meta($e->$id_field, 'admin')) == 1){
				unset($elements[$k]);
				$break = true;
			}

			if(!$break){
				$current_dynamic_parent = false;

				if(SubmenuModel::get_meta($e->$id_field, 'autopopulate') == 1){
					
					$type = SubmenuModel::get_meta($e->$id_field, 'populate-type');
					$value = SubmenuModel::get_meta($e->$id_field, 'populate-value');

					if($type == 'post'){

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

						$test = new WP_Query($post_query);

						if($test->have_posts()){
							foreach($test->posts as $p){
								$p->$id_field = $p->ID;
								$p->title = $p->post_title;
								
								if(is_single($post) && is_singular( $value ) && $post->ID == $p->ID){
									$current_dynamic_parent = true;
									$p->classes = array('current-menu-item');
								}
								
								$p->url = get_permalink( $p->ID);
								$p->$parent_field = $e->$id_field;
								$elements[] = clone($p);
							}
						}
					}elseif($type == 'tax'){

						$dynamic_item_prefix = str_repeat(0, $dynamic_count);

						$tax_parent_id = $e->$id_field;
						
						$order = SubmenuModel::get_meta($tax_parent_id, 'tax-order');
						$orderby = SubmenuModel::get_meta($tax_parent_id, 'tax-orderby');
						$hide = SubmenuModel::get_meta($tax_parent_id, 'tax-empty');

						$terms = get_terms( $value, array(
							'hide_empty' => $hide,
							'order' => $order,
							'order_by' => $orderby
						) );

						$tax_elements = array();

						foreach($terms as $t){
							// $t->$id_field = $t->term_id;
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

							if((is_category() && is_category( $t->ID )) || (is_tag() && is_tag( $t->slug )) || is_tax( $value, $t->slug ) ){
								$current_dynamic_parent = $t->$parent_field;
								$t->classes = array('current-menu-item');
							}
							
							$tax_elements[] = clone($t);
						}

						if($current_dynamic_parent){
							$this->child_page_walker($tax_elements, $current_dynamic_parent);
						}

						$elements = array_merge($elements, $tax_elements);

						$dynamic_count++;

					}elseif($type == 'page'){

						$pages = get_pages(array( 
							'hierarchical' => 1, 
							'child_of' => $value 
						));

						$page_elements = array();

						foreach($pages as $p){
							$p->$id_field = $p->ID;
							$p->title = $p->post_title;

							$p->url = get_permalink( $p->ID);
							if($p->post_parent == $value){
								$p->$parent_field = $e->$id_field;
							}else{
								$p->$parent_field = $p->post_parent;
							}

							if( is_page($p->ID) && $post->ID == $p->ID){
								$current_dynamic_parent = $p->$parent_field;
								$p->classes = array('current-menu-item');
							}
							
							$page_elements[] = clone($p);
						}

						if($current_dynamic_parent){
							$this->child_page_walker($page_elements, $current_dynamic_parent);
						}

						$elements = array_merge($elements, $page_elements);
						
					}
				}
			
				if($current_dynamic_parent){
					$e->classes[] = 'current-menu-ancestor';
				}

			}
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

	public function child_page_walker(&$elements, $parent){

		$parent_field = $this->db_fields['parent'];

		foreach($elements as &$p){

			if($p->ID == $parent){
				$p->classes = array('current-menu-ancestor');
				$this->child_page_walker($elements, $p->$parent_field);
				continue;
			}
			
		}
	}
}

class Walker_Nav_Menu_Dropdown extends JC_Submenu_Nav_Walker{
 
	var $item_id = 0;
 
	function __construct($id = 0){
		$this->item_id = $id;
	}
 
	public function start_lvl(&$output, $depth){}
 
	public function end_lvl(&$output, $depth){}
 
	public function start_el(&$output, $item, $depth, $args){
 
		$item->title = str_repeat("&nbsp;", $depth * 4) . $item->title;
 
		parent::start_el($output, $item, $depth, $args);
		if($item->ID == $this->item_id)
			$output = str_replace('<li', '<option value="'.$item->ID.'" selected="selected"', $output);
		else
			$output = str_replace('<li', '<option value="'.$item->ID.'"', $output);
	}
 
	public function end_el(&$output, $item, $depth){
		$output .= "</option>\n";
	}
}

?>