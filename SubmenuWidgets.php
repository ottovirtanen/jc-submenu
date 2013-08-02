<?php
/**
 * Submenu Widget Class
 *
 * Display Wordpress Submenu Widget
 * 
 * @author James Collings <james@jclabs.co.uk>
 * @version 0.0.1
 */
class JC_Adv_Menu_Widget extends WP_Widget {
 	
 	/**
 	 * Register Widget
 	 */
	public function __construct() {
		parent::__construct(
	 		'jc_menu_widget', // Base ID
			'JC Submenu Advanced Menu Widget', // Name
			array( 'description' => __( 'Custom options to output menus in your theme')) // Args
		);
	}
 	
 	/**
 	 * Widget Output
 	 * @param  array $args     
 	 * @param  array $instance 
 	 * @return void           
 	 */
	public function widget( $args, $instance ) {
		extract( $args );
 
		$title = apply_filters( 'widget_title', $instance['title'] );
 
		echo $before_widget;
 
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
 
		wp_nav_menu( array('menu' => $instance['menu'], 'walker' => new JC_Submenu_Nav_Walker(array(
			'menu_item' => $instance['menu_item'], 
			'hierarchical' => $instance['menu_hierarchy']
			))
		));
 
		echo $after_widget;
	}
 	
 	/**
 	 * Widget Options
 	 * @param  array $instance 
 	 * @return void
 	 */
 	public function form( $instance ) {
 
		$title = isset($instance['title']) ? $instance['title'] : '';
		$menu = isset($instance['menu']) ? $instance['menu'] : '';
		$menu_item = isset($instance['menu_item']) ? $instance['menu_item'] : '';
		$menu_hierarchy = isset($instance['menu_hierarchy']) ? $instance['menu_hierarchy'] : 1;
		$menus = get_terms('nav_menu');
 
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
 
		<p>
		<input class="widefat" id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>" type="hidden" value="<?php echo $menu; ?>" />
		</p>
 
		<p>
			<label for="<?php echo $this->get_field_id( 'menu_item' ); ?>"><?php _e( 'Select Menu Part:' ); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'menu_item' ); ?>" name="<?php echo $this->get_field_name( 'menu_item' ); ?>" >
				<?php foreach($menus as $m){
					wp_nav_menu(array(
					  'menu' => $m->slug, // your theme location here
					  'container' => false,
					  'walker'         => new Walker_Nav_Menu_Dropdown($menu_item),
					  'items_wrap'     => '<optgroup id="'.$m->slug.'" label="'.$m->name.'">%3$s</optgroup>',
					));
				} ?>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'menu_hierarchy' ); ?>" name="<?php echo $this->get_field_name( 'menu_hierarchy' ); ?>" type="checkbox" value="1" <?php if($menu_hierarchy == 1): ?>checked="checked"<?php endif; ?> />
			<label for="<?php echo $this->get_field_id( 'menu_hierarchy' ); ?>">Show Hierarchy</label>
		</p>
 
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('select#<?php echo $this->get_field_id( 'menu_item' ); ?>').change(function(){
				var label=$('select#<?php echo $this->get_field_id( 'menu_item' ); ?> :selected').parent().attr('id');
			    $('#<?php echo $this->get_field_id( 'menu' ); ?>').val(label);
			});
		});
		</script>
 
		<?php 
	}
 	
 	/**
 	 * Save Widget Options
 	 * @param  array $new_instance 
 	 * @param  array $old_instance 
 	 * @return array
 	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['menu'] = strip_tags( $new_instance['menu'] );
		$instance['menu_item'] = strip_tags( $new_instance['menu_item'] );
		$instance['menu_hierarchy'] = intval( $new_instance['menu_hierarchy'] );
 
		return $instance;
	}
 
}
 
add_action( 'widgets_init', create_function( '', 'register_widget( "JC_Adv_Menu_Widget" );' ) );