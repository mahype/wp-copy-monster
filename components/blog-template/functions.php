<?php

add_cm_plugin( 'blog-template', __( 'Template Settings', 'copy-monster-options' ), 'copy_monster_template_admin', '', 'copy_monster_template_save', 0 );

function copy_monster_template_admin(){ 
	global $wpdb;
	
    $blogs = $wpdb->get_results( "SELECT blog_id FROM " . $wpdb->blogs. " WHERE spam = '0' AND deleted ='0'", ARRAY_A );
    
	$cm_template_options = get_option( CM_TEMPLATE_OPTIONS );
	
	if( !is_array( $cm_template_options ) )
		$content.= '<p>' . __( 'No Template have been created yet. Please create one to go further.', 'copy-monster-options' ) . '</p>';
	
	if( CM_DEFAULT_EDIT_TEMPLATE_ID == 0  )
		$content.= '<p>' . __( 'Please select an Template which you want to edit to go further.', 'copy-monster-options' ) . '</p>';
	
	if( is_array( $cm_template_options ) ):
		$content.= '<p><table class="wp-list-table widefat fixed posts" cellspacing="3" cellpadding="3">';
		
		$content.= '<thead>';
			$content.= '<tr valign="top">';
				$content.= '<th>' . __( 'Template', 'copy-monster-options' ) . '</th>';	
				$content.= '<th>' . __( 'Blog', 'copy-monster-options' ) .'</th>';
				$content.= '<th>' . __( 'Edit Template', 'copy-monster-options' ) .'</th>';
				$content.= '<th colspan="2">' . __( 'Select as Default Template for new created blogs', 'copy-monster-options' ) .'</th>';
	        $content.= '</tr>';
		$content.= '</thead>';
		
		$content.= '<tbody>';
		
		foreach( $cm_template_options AS $key => $template_options ):
			
			if( is_int( $key ) ):
				$checked_template = '';
				$checked_copy_monster = '';
				
				if( CM_EMPLATE_EDIT_ID == $template_options[ 'template_id' ] && '' != CM_EMPLATE_EDIT_ID )
					$checked_template = ' checked="checked"';
					
				if( CM_TEMPLATE_ID == $template_options[ 'template_id' ] && '' != CM_TEMPLATE_ID  )
					$checked_copy_monster = ' checked="checked"';
				
				$content.= '<tr>';
					$content.= '<td>' . $template_options[ 'template_name' ] . '</td>';	
					$content.= '<td>' . get_blog_option( $template_options['blog_id'], 'blogname' ) .'</td>';
					$content.= '<td><input type="radio" name="' . CM_OPTION_GROUP . '[cm_template_edit_id]" value="' . $template_options[ 'template_id' ] . '"' . $checked_template . ' /></td>';
					$content.= '<td><input type="radio" name="' . CM_OPTION_GROUP . '[cm_template_id]" value="' . $template_options[ 'template_id' ] . '"' . $checked_copy_monster . ' /></td>';
					$content.= '<td><a href="#" class="button delete_template" rel="' . $template_options[ 'template_id' ] . '" />' . __( 'Delete Template', 'copy-monster-options' ) . '</a></td>';
					$content.= '';
		        $content.= '</tr>';
			endif;
			
		endforeach;
		
		$content.= '</tbody>';
		
		$content.= '</table></p>';
	endif;
	
	////////////
	
	$content.= '<h3>' . __( 'Create Blog Template', 'copy-monster-options' ) . '</h3>';
	
	$content.= '<p><table class="wp-list-table widefat fixed posts" cellspacing="3" cellpadding="3">';
	$content.= '<tbody>';
		$content.= '<tr>';
			
		$content.= '<td><label for="create_blog_template_name">' . __( 'Template name', 'copy-monster-options' ) . '</label>';
		
		$content.= '<p><input type="text" id="create_blog_template_name" name="' . CM_OPTION_GROUP . '[create_blog_template_name]" /></p></td>';
		
		$content.= '<td><label for="create_blog_template_name">' . __( 'Select a Blog', 'copy-monster-options' ) . '</label>';
		
		$content.= '<p><select id="create_blog_id" name="' . CM_OPTION_GROUP . '[create_blog_id]">';
		
		$content.= '<option selected value="none">' . __( 'Please select a blog', 'copy-monster-options' ) . '</option>\n';
	 
	    foreach( $blogs AS $blog ):
			$content.= '<option value="' . $blog['blog_id'] . '">' . get_blog_option( $blog['blog_id'], 'blogname' ) . '</option>\n';
	    endforeach;
	
	    $content.= '</select></p></td>';
		
		$content.= '<td>' . tk_form_button( __( 'Add new Template', 'copy-monster-options' ), array( 'name' => 'new_template' ), 'html' ) . '</td>';
		
		$content.= '</tr>';
		
	$content.= '</tbody>';
	$content.= '</table></p>';
	
	$content.='<input type="hidden" name="delete_template_id" id="delete_template_id" />';
	
	$content = apply_filters( 'copy-monster-template-admin', $content );
	
	echo $content;
	
	do_action( 'copy-monster-template-admin-bottom' );
}

function copy_monster_template_save( $input ){
	$cm_template_options = get_option( CM_TEMPLATE_OPTIONS );
	
	if( isset( $_REQUEST[ 'new_template' ] ) && 'none' != $input[ 'create_blog_id' ] && '' != $input[ 'create_blog_template_name' ] ):
		
		if( is_array( $cm_template_options ) ):
			$last_inserted_id = $cm_template_options[ 'last_inserted_id' ];
			$template_id = $last_inserted_id + 1;
		else:
			$cm_template_options = array();
			$template_id = 1;
			$last_inserted_id = 1;
		endif;
		
		$cm_template_options[ $template_id ] = array(
			'blog_id' => $input[ 'create_blog_id' ],
			'template_id' => $template_id,
			'template_name' => $input[ 'create_blog_template_name' ],
		);
		
		$cm_template_options[ 'last_inserted_id' ] = $template_id;
		
		update_option( CM_TEMPLATE_OPTIONS, $cm_template_options );
		
	elseif( '' != $_REQUEST[ 'delete_template_id' ] ):
		
		foreach ( $cm_template_options AS $key => $template ):
			if( $template[ 'template_id' ] == $_REQUEST[ 'delete_template_id' ] ):
				unset( $cm_template_options[ $key ] );
				break;
			endif;
		endforeach;
		
		update_option( CM_TEMPLATE_OPTIONS, $cm_template_options );
	endif;
	
	unset( $input[ 'delete_template_id' ] );
	unset( $input[ 'create_blog_id' ] );
	unset( $input[ 'create_blog_template_name' ] );
	
	return $input;
}
