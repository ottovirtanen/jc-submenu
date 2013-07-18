// @TODO: Rewrite javascript
jQuery(document).ready(function($) {
	// Show hide post/tax/child options on menu
	$('input.jc-submenu-populate-type:checked').each(function(){
		toggle_radio_buttons($(this));
	});
	$('input.jc-submenu-populate-type').live('change', function(){
		toggle_radio_buttons($(this));
	});

	// show hide populate blocks
	$('input.jc-submenu-autopopulate').each(function(){
		toggle_check_buttons($(this));
	});
	$('input.jc-submenu-autopopulate').live('change', function(){
		toggle_check_buttons($(this));
	});

	function toggle_check_buttons(this_elem){
		var id = jc_submenu_get_menu_id(this_elem.attr('id'));

		$('.jc-submenu-populate-block', $('#menu-item-'+id)).each(function(){
			$(this).hide();
		});

		$('input.jc-submenu-autopopulate:checked').each(function(){
			id = jc_submenu_get_menu_id($(this).attr('id'));
			$( '.jc-submenu-populate-block' , $('#menu-item-'+id) ).show();
		});
	}

	function toggle_radio_buttons(this_elem){
		var id = jc_submenu_get_menu_id(this_elem.attr('id'));

		$('.show-post, .show-tax, .show-page', $('#menu-item-'+id)).each(function(){
			$(this).hide();
		});

		$('input.jc-submenu-populate-type:checked').each(function(){
			id = jc_submenu_get_menu_id($(this).attr('id'));
			$( '.show-'+$(this).val() , $('#menu-item-'+id) ).show();
		});
	}

	function jc_submenu_get_menu_id(id){
		id= id.split("-");
		id = id[id.length-1];
		return id;
	}
	
});

