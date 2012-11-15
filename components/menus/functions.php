<?php

add_cm_plugin( 'menus', __( 'Menus', 'copy-monster-options' ), 'copy_monster_menus_admin', 'copy_monster_menus_copy', 'copy_monster_menus_save', 10 );

function copy_monster_menus_admin(){ 
	global $wpdb, $copy_monster_template;
	
	switch_to_blog( DFB_TEMPLATE_EDIT_BLOG_ID );
	
	$nav_menus = wp_get_nav_menus( array('orderby' => 'name') );
	
	$checked_nav_menus = $copy_monster_template[ 'nav_menu' ];
	
	$elements = array();
	
	$content = '<h3>' . __( 'Menus', 'copy-monster-options' )  . '</h3>';
	$content.= '<p>' . __( 'Select the menus you want to copy.', 'copy-monster-options' )  . '<p>';
	
	$content.= '<table class="widefat">';
		
	$content.= '<thead>';
	$content.= '<tr>';
		$content.= '<th>' . __( 'Menu', 'copy-monster-options' ) . '</th>';	
		$content.= '<th>' . __( 'Copy', 'copy-monster-options' ) . '</th>';
    $content.= '</tr>';
	$content.= '</thead>';
	
	$content.= '<tbody>';
	
	if( is_array( $nav_menus ) && count( $nav_menus ) > 0 ):
		foreach( $nav_menus AS $nav_menu ):
			$nav_menu_checked = '';
			
			// Is Nav Menu checked for a copy?
			if( is_array( $checked_nav_menus ) )
				if( in_array(  $nav_menu->term_id, $checked_nav_menus ) ) 
					$nav_menu_checked = ' checked';
			
			$content.= '<tr>';
				$content.= '<td>' . $nav_menu->name . '</td>';	
				$content.= '<td><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][nav_menu][]" value="' . $nav_menu->term_id . '"' . $nav_menu_checked . ' /></td>';
	        $content.= '</tr>';
		endforeach;
	else:
		$content.= '<tr>';
			$content.= '<td colspan="2">' . __( 'No Entry was found', 'copy-monster-options' ) . '</td>';	
        $content.= '</tr>';
	endif;
	
	$content.= '</tbody>';
	$content.= '</table><br />';
	
	if( is_array( $copy_monster_template[ 'nav_menu' ] ) ):
	
		foreach( $copy_monster_template[ 'nav_menu' ] AS $nav_menu_id ):
			
			$nav_menu = wp_get_nav_menu_object( $nav_menu_id );
			$nav_menu_items = wp_get_nav_menu_items( $nav_menu_id );
			$checked_nav_menu_items = $copy_monster_template[ 'nav_menu_items' ][ $nav_menu_id ];
			
			$content_tab = '<h3>' . __( 'Menu Items', 'copy-monster-options' )  . '</h3>';
			$content_tab.= '<p>' . __( 'Select the menu items you want to copy.', 'copy-monster-options' )  . '<p>';
			
			$content_tab.= '<table class="widefat">';
			
			$content_tab.= '<thead>';
			$content_tab.= '<tr>';
				$content_tab.= '<th>' . __( 'Title', 'copy-monster-options' ) . '</th>';	
				$content_tab.= '<th>' . __( 'Copy', 'copy-monster-options' ) . '</th>';
	        $content_tab.= '</tr>';
			$content_tab.= '</thead>';
			
			$content_tab.= '<tbody>';
			
			foreach( $nav_menu_items AS $nav_menu_item ):
			
				$nav_menu_item_checked = '';
				
				// Is Nav Menu Item checked for a copy?
				if( is_array( $checked_nav_menu_items ) )
					if( in_array( $nav_menu_item->ID, $checked_nav_menu_items ) ) 
						$nav_menu_item_checked = ' checked';
				
				$content_tab.= '<tr>';
					$content_tab.= '<td>' .$nav_menu_item->title . '</td>';	
					$content_tab.= '<td><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][nav_menu_items][' . $nav_menu->term_id . '][]" value="' . $nav_menu_item->ID . '"' . $nav_menu_item_checked . ' /></td>';
		        $content_tab.= '</tr>';
			endforeach;
			
			$content_tab.= '</table>';
			
			$elements[] = array(
				'id' => 'cm_' . $nav_menu->slug,
				'title' => $nav_menu->name,
				'content' => $content_tab
			);	
		endforeach;
	
	endif;
	
	$content.= tk_tabs( 'copy_monster_menu_tabs', $elements, 'html' );
		
	$content = apply_filters( 'copy_monster_menus_admin', $content );
	
	echo $content;
	
	do_action( 'copy_monster_links_admin_bottom' );
	
	restore_current_blog();
}

function copy_monster_menus_copy( $from_blog_id, $to_blog_id ){
	global $copy_monster_template, $copy_monster_menu_references;
	
	$checked_nav_menus = $copy_monster_template[ 'nav_menu' ];
	
	if( is_array( $checked_nav_menus ) ):
	
		// Copy all Nav Menus
		foreach( $checked_nav_menus AS $nav_menu_id ):
			
			// Getting actual Nav Menu
			switch_to_blog( $from_blog_id );
			$nav_menu = wp_get_nav_menu_object( $nav_menu_id );
			restore_current_blog();
			
			// Creating Nav Menu
			switch_to_blog( $to_blog_id );
			$new_nav_menu_id = wp_create_nav_menu( $nav_menu->name );
			restore_current_blog();
			
			$copy_monster_menu_references[ $nav_menu->term_id ] = $new_nav_menu_id;
			
			// Copy all Items of Menu
			copy_monster_copy_menu_items( $from_blog_id, $to_blog_id, $nav_menu->term_id, $new_nav_menu_id );
		endforeach;
		
		// Setting up nav menu locations
		switch_to_blog( $from_blog_id );
		$nav_menu_locations = get_theme_mod( 'nav_menu_locations' );
		restore_current_blog();
		
		// Writing new Ids in Nav location Array
		foreach( $nav_menu_locations AS $nav_menu_name => $nav_menu_location ):
			$nav_menu_locations[ $nav_menu_name ] = $copy_monster_menu_references[ $nav_menu_locations[ $nav_menu_name ] ];
		endforeach;
		
		// Saving nav menu locations to new blog
		switch_to_blog( $to_blog_id );
		set_theme_mod( 'nav_menu_locations', $nav_menu_locations );
		restore_current_blog();
		
	endif;
}
function copy_monster_copy_menu_items( $from_blog_id, $to_blog_id, $from_nav_menu_id, $to_nav_menu_id, $args = array() ){
	global $copy_monster_template;
	
	$defaults = array(
		'menu_item_ids' => $copy_monster_template[ 'nav_menu_items' ][ $from_nav_menu_id ]
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	// Creating Nav Menu items
	if( is_array( $menu_item_ids ) ):
		foreach( $menu_item_ids AS $menu_item_id ):
			copy_monster_copy_menu_item( $from_blog_id, $to_blog_id, $from_nav_menu_id, $to_nav_menu_id, $menu_item_id );
		endforeach;
	endif;
}
function copy_monster_copy_menu_item( $from_blog_id, $to_blog_id, $from_nav_menu_id, $to_nav_menu_id, $menu_item_id ){
	global $copy_monster_post_relations, $copy_monster_term_relations;
	
	// Getting all posts of post type
	$args = array(
		'post_type' => 'nav_menu_item',
		'post__in' => array( $menu_item_id ) // Only taking selected posts
	);
	
	// Getting Posts from Soiurce Blog
	switch_to_blog( $from_blog_id );
	$the_query = new WP_Query( $args );
	restore_current_blog();
	
	// Running Posts of Post Type
	while ( $the_query->have_posts() ) : $the_query->the_post();
		global $post;
	
		switch_to_blog( $from_blog_id );
		$nav_menu_item_post = wp_setup_nav_menu_item( $post );
		restore_current_blog();
		
		if( 'post_type' == $nav_menu_item_post->type )
			$object_id = $copy_monster_post_relations[ $nav_menu_item_post->object_id ];
			
		if( 'taxonomy' == $nav_menu_item_post->type )
			$object_id = $copy_monster_term_relations[ $nav_menu_item_post->object_id ];
			
		if( 'custom' == $nav_menu_item_post->type )
			$object_id = 0;
	
		$menu_item_data = array(
			'menu-item-object-id' => $object_id,
			'menu-item-object' => $nav_menu_item_post->object,
			'menu-item-parent-id' => $nav_menu_item_post->menu_item_parent,
			'menu-item-position' => $nav_menu_item_post->menu_order,
			'menu-item-type' => $nav_menu_item_post->type,
			'menu-item-title' => apply_filters( 'cm_menu_item_title', $nav_menu_item_post->title, $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-url' => apply_filters( 'cm_menu_item_url', $nav_menu_item_post->url, $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-description' =>  apply_filters( 'cm_menu_item_description', $nav_menu_item_post->description, $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-attr-title' => apply_filters( 'cm_menu_item_attr_title', $nav_menu_item_post->attr_title, $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-target' =>  apply_filters( 'cm_menu_item_target', $nav_menu_item_post->target, $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-classes' => apply_filters( 'cm_menu_item_classes', implode( ' ', $nav_menu_item_post->classes ), $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-xfn' => apply_filters( 'cm_menu_item_xfn', $nav_menu_item_post->xfn, $from_blog_id, $to_blog_id, $menu_item_id ),
			'menu-item-status' => $nav_menu_item_post->post_status,
		);
		
		switch_to_blog( $to_blog_id );
		wp_update_nav_menu_item( $to_nav_menu_id, 0, $menu_item_data );
		restore_current_blog();
	endwhile;
}
function copy_monster_get_menu_item( $blog_id, $nav_menu_id, $menu_item_id ){
	
	// Getting Nav Menu Object
	switch_to_blog( $blog_id );
		$nav_menu_items = wp_get_nav_menu_items( $nav_menu_id );
		
		if( is_array( $nav_menu_items ) ):
			foreach( $nav_menu_items AS $nav_menu_item ):
				if( $nav_menu_item->term_id == $menu_item_id )
					break;
			endforeach;
		endif;
	restore_current_blog();
	
	return $nav_menu_item;
}
// REMEMBER to copy locations! get_nav_menu_locations

function copy_monster_get_nav_menu_item_from_post( $post ){
	
}
function copy_monster_menus_save( $input ){
	return $input;
}
