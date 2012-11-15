<?php

add_cm_plugin( 'blog_options', __( 'Blog Options', 'copy-monster-options' ), 'copy_monster_options_admin', 'copy_monster_options_copy', 'copy_monster_options_save', 35 );

function copy_monster_options_admin(){ 
	global $wpdb, $copy_monster_template;
	
	switch_to_blog( DFB_TEMPLATE_EDIT_BLOG_ID );
	
	$options_table = $wpdb->base_prefix . DFB_TEMPLATE_EDIT_BLOG_ID . '_options';
	
	$options = $wpdb->get_results( "SELECT * FROM " . $options_table . " ORDER BY option_name");
	
	// If there is no result
	if( count( $options ) == 0 )
		$options_table = $wpdb->base_prefix . '_options';
	
	$options = $wpdb->get_results( "SELECT * FROM " . $options_table . " ORDER BY option_name");
	
	$content = '<table class="widefat">';
	
	$content.= '<thead>';
		$content.= '<tr>';
			$content.= '<th>' . __( 'Name', 'copy-monster-options' ) . '</th>';	
			$content.= '<th>' . __( 'Value', 'copy-monster-options') .'</th>';
			$content.= '<th>' . __( 'Copy', 'copy-monster-options' ) . '</th>';
	    $content.= '</tr>';
	$content.= '</thead>';
	
	$content.= '<tbody>';
	
		$options_selected = $copy_monster_template['options'];
		
		foreach( (array) $options as $option) :
			$option->option_name = esc_attr( $option->option_name );
			
			$checked = '';
			
			if( is_array( $options_selected ) )
				if( in_array( $option->option_name, $options_selected ) )
					$checked = ' checked';
			
			$content.= '<tr>';
				$content.= '<td><label for="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][options][]">' . $option->option_name . '</label></td>';
				$content.= '<td><textarea disabled="disabled">' . $option->option_value . '</textarea></td>';
				$content.= '<td><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][options][]" value="' . $option->option_name . '" ' . $checked . ' /></td>';
			$content.= '<tr>';
			
			$content = apply_filters( 'copy-monster-options-row', $content, $option->option_name );
		
		endforeach;
		
	$content.= '</tbody>';
	
	$content.= '</table>';
	
	$content = apply_filters( 'copy-monster-options-admin', $content );
	
	echo $content;
	
	do_action( 'copy-monster-options-admin-bottom' );
	
	restore_current_blog();
}

function copy_monster_options_copy( $from_blog_id, $to_blog_id ){
	global $wp_rewrite, $copy_monster_template;
	
	$options = $copy_monster_template[ 'options' ];
	
	if( is_array( $options ) ):
		foreach( $options AS $option ):
			switch_to_blog( $to_blog_id );
				
				if( 'permalink_structure' == $option ):
					$wp_rewrite->set_permalink_structure( get_blog_option( $from_blog_id, $option ) );
				
				elseif( 'category_base' == $option ):
					$wp_rewrite->set_category_base( get_blog_option( $from_blog_id, $option ) );
					
				elseif( 'tag_base' == $option  ):
					$wp_rewrite->set_tag_base( get_blog_option( $from_blog_id, $option ) );
					
				else:
					update_option( $option, get_blog_option( $from_blog_id, $option ) );
					
				endif;
				
				create_initial_taxonomies();
				
				$wp_rewrite->flush_rules();
				
			restore_current_blog();
		endforeach;
	endif;
}

function copy_monster_options_save( $input ){
	return $input;
}
