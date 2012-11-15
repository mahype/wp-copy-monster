<?php

add_cm_plugin( 'posts', __( 'Post Types', 'copy-monster-options' ), 'copy_monster_posts_admin', 'copy_monster_posts_copy', 'copy_monster_posts_save', 5  );

function copy_monster_posts_admin(){ 
	global $wpdb, $copy_monster_template;
	
	switch_to_blog( DFB_TEMPLATE_EDIT_BLOG_ID );
	
	$post_types = apply_filters( 'copy_monster_post_types', copy_monster_get_post_types_db( get_post_types( '', 'object' ) ) );
	
	// $post_types = apply_filters( 'copy_monster_post_types',get_post_types( '', 'object' ) );
	
	$elements = array();
	
	foreach( $post_types AS $post_type ):
		
		// Table head
		$content = '<h3>' . $post_type->labels->all_items . '</h3>';
		
		$content.= '<table class="widefat">';
		
		$content.= '<thead>';
			$content.= '<tr>';
				$content.= '<th>' . __( 'Title', 'copy-monster-options' ) . '</th>';	
				$content.= '<th>' . __( 'Date', 'copy-monster-options') .'</th>';
				$content.= '<th>' . __( 'Status', 'copy-monster-options') .'</th>';
				$content.= '<th>' . __( 'Copy', 'copy-monster-options' ) . '</th>';
				$content.= '<th>' . __( 'Attachments', 'copy-monster-options' ) . '</th>';
				$content.= '<th>' . __( 'Meta', 'copy-monster-options' ) . 	'</th>';
				
				if( post_type_supports( $post_type->name, 'comments' ) )
					$content.= '<th>' . __( 'Comments', 'copy-monster-options' ) . '</th>';	       
	        
	        $content.= '</tr>';
		$content.= '</thead>';
		
		$checked_posts = $copy_monster_template[ $post_type->name ];
		$checked_posts_attachments = $copy_monster_template[ $post_type->name . '_attachments' ];
		$checked_posts_meta = $copy_monster_template[ $post_type->name . '_meta' ];
		
		if( post_type_supports( $post_type->name, 'comments' ) )
			$checked_posts_comments = $copy_monster_template[ $post_type->name . '_comments' ];
		
		// Getting older version values 
		if( '' == $checked_posts ):
			if( 'post' == $post_type->name )
				if( is_array( $copy_monster_template[ 'posts' ] ) ) $checked_posts = $copy_monster_template[ 'posts' ];
			if( 'page' == $post_type->name )
				if( is_array( $copy_monster_template[ 'pages' ] ) ) $checked_posts = $copy_monster_template[ 'pages' ];
		endif;
			
		$content.= '<tbody>';
		
		// Getting all posts of post type
		$args = array(
			'post_type' => $post_type->name,
			'posts_per_page' => -1 // Show all posts
		);
		
		$the_query = new WP_Query( $args );
		
		while ( $the_query->have_posts() ) : $the_query->the_post();
			global $post;
				$status = '';
				
				$post_checked = '';
				$post_meta_checked = '';
				$post_attachments_checked = '';
				$post_comments_checked = '';
				
				// Is Post checked for a copy?
				if( is_array( $checked_posts ) )
					if( in_array( get_the_ID(), $checked_posts ) ) 
						$post_checked = ' checked';
						
				// Is Post attachment checked for copy?
				if( is_array( $checked_posts_attachments ) )
					if( in_array( get_the_ID(), $checked_posts_attachments ) ) 
						$post_attachments_checked = ' checked';
					
				// Is Post meta checked for copy?
				if( is_array( $checked_posts_meta ) )
					if( in_array( get_the_ID(), $checked_posts_meta ) ) 
						$post_meta_checked = ' checked';
					
				// Is Post comments checked for a copy?
				if( is_array( $checked_posts_comments ) && post_type_supports( $post_type->name, 'comments' ) )
					if( in_array( get_the_ID(), $checked_posts_comments ) ) 
						$post_comments_checked = ' checked';
				
				switch( $post->post_status ){
					case 'new':
						$status = _x( 'New', 'Post status', 'copy-monster-options' );
						break;
					case 'publish':
						$status = _x( 'Published', 'Post status', 'copy-monster-options' );
						break;
					case 'pending':
						$status = _x( 'Pending', 'Post status', 'copy-monster-options' );
						break;
					case 'draft':
						$status = _x( 'Draft', 'Post status', 'copy-monster-options' );
						break;
					case 'auto-draft':
						$status = _x( 'Auto Draft', 'Post status', 'copy-monster-options' );
						break;
					case 'future':
						$status = _x( 'Future', 'Post status', 'copy-monster-options' );
						break;
					case 'private':
						$status = _x( 'Private', 'Post status', 'copy-monster-options' );
						break;
					case 'inherit':
						$status = _x( 'Inherit', 'Post status', 'copy-monster-options' );
						break;
					case 'trash':
						$status = _x( 'Trash', 'Post status', 'copy-monster-options' );
						break;
				}
				
				$content.= '<tr>';
				
				$content.= '<td>';
				$content.= get_the_title();
				$content.= '</td>';
				
				$content.= '<td>';
				$content.= get_the_date();
				$content.= '</td>';
				
				$content.= '<td>';
				$content.= $status;
				$content.= '</td>';
				
				$content.= '<td>';
				$content.= '<input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][' . $post_type->name . '][]" value="' . get_the_ID() . '"' . $post_checked . ' />';
				$content.= '</td>';
				
				$content.= '<td>';
				$content.= '<input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][' . $post_type->name . '_attachments][]" value="' . get_the_ID() . '"' . $post_attachments_checked . ' />';
				$content.= '</td>';
				
				$content.= '<td>';
				$content.= '<input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][' . $post_type->name . '_meta][]" value="' . get_the_ID() . '"' . $post_meta_checked . ' />';
				$content.= '</td>';
				
				if( post_type_supports( $post_type->name, 'comments' ) ):
					$content.= '<td>';
					$content.= '<input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][' . $post_type->name . '_comments][]" value="' . get_the_ID() . '"' . $post_comments_checked . ' /> <span class="comment-count">' . get_comments_number() . '</span>';
					$content.= '</td>';
				endif;
				
			$content.= '</tr>';
		endwhile;
		
		$content.= '</tbody>';
		$content.= '</table>';
		
		// Delete automatic entries
		if( $post_type->name == 'post' || $post_type->name == 'page' ):
			if( $copy_monster_template[ $post_type->name . '_delete_existing' ] )
				$post_type_delete_existing_checked = ' checked="checked"';
			
			$content.= '<p><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][' . $post_type->name . '_delete_existing]" value="true"' . $post_type_delete_existing_checked . ' /> ';
			$content.=  __( 'Delete automatic generated WordPress entries.', 'copy_monster_options' ) . '</p>';
			
		endif;
		
		// copy_monster_get_post_type_taxonomies_db( $post_type->name );
		
		$taxonomies = copy_monster_get_taxonomies_db( $post_type->name );
		
		// $taxonomies = get_taxonomies( array( 'object_type' => array( $post_type->name ) ), 'objects' );
		
		// Taxonomies
		if( is_array( $taxonomies ) ):
			foreach( $taxonomies AS $taxonomy ):
				
				// $terms = get_terms( $taxonomy->name, array( 'hide_empty' => FALSE ) );
				
				$terms = copy_monster_get_terms_db( $taxonomy->name );
				
				$content.= '<h3>' . $taxonomy->labels->name . '</h3>';
				
				if( count( $terms ) > 0 ):
				
					$content.= '<table class="widefat">';
					
					$content.= '<thead>';
						$content.= '<tr>';
							$content.= '<th>' . __( 'Title', 'copy-monster-options' ) . '</th>';	
							$content.= '<th>' . __( 'Copy Term', 'copy-monster-options' ) . '</th>';
				        $content.= '</tr>';
					$content.= '</thead>';
					
					$content.= '<tbody>';
					
					foreach( $terms AS $term ):
						$term_checked = '';
						if( is_array( $copy_monster_template[ $post_type->name . '_taxonomies' ][ $taxonomy->name ] ) )
							if( in_array( $term->term_id, $copy_monster_template[ $post_type->name . '_taxonomies' ][ $taxonomy->name ] ) )
								$term_checked = ' checked="checked"';
						
						$content.= '<tr>';
							$content.= '<td>' . $term->name . '</td>';	
							$content.= '<td><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][' . $post_type->name. '_taxonomies][' . $taxonomy->name  . '][]" value="' . $term->term_id . '"' . $term_checked . ' />';
				        $content.= '</tr>';
					endforeach;
					
					$content.= '</tbody>';
		
					$content.= '</table>';
				
				else:
					$content.= '<p>' . __( 'No entry found.	', 'copy-monster-options' ) . '</p>';
				endif;
				
			endforeach;
		endif;
		
		$content = apply_filters( 'copy-monster-posts-'. $post_type->name, $content );
		
		$elements[] = array(
			'id' => sanitize_title( $post_type->name ),
			'title' => $post_type->label,
			'content' => $content
		);		
	endforeach;
		
	$content = tk_tabs( 'copy_monster_post_tabs', $elements, 'html' );
	
	$content = apply_filters( 'copy-monster-posts-admin', $content );
	
	echo $content;
	
	do_action( 'copy-monster-posts-admin-bottom' );
	
	restore_current_blog();
	
}

function copy_monster_posts_copy( $from_blog_id, $to_blog_id, $args = array() ){
	global $copy_monster_template;
	
	// Setting up Post Types
	$defaults = array(
		'post_types' => apply_filters( 'copy_monster_post_types', copy_monster_get_post_types( get_post_types( '', 'object' ) ) ),
		'template_id' => CM_TEMPLATE_ID
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	// Running Post Types	
	foreach( $post_types AS $post_type ):
		
		// Copy Taxonomies
		$taxonomies = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'objects' );
		$taxonomy_termlist = copy_monster_copy_taxonomies( $post_type, $from_blog_id, $to_blog_id );
		
		if( $copy_monster_template[ $post_type . '_delete_existing' ] ):
			switch_to_blog( $to_blog_id );
			
			$args = array(
				'numberposts' => -1,
				'post_type' => $post_type
			);
			$delete_posts = get_posts( $args );
			
			foreach( $delete_posts AS $delete_post ):
				wp_delete_post( $delete_post->ID, TRUE );
			endforeach;
			
			restore_current_blog();
		endif;
		
		// Getting all posts of post type
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1, // Show all posts
			'post__in' => $copy_monster_template[ $post_type ] // Only taking selected posts
		);
		
		// Getting Posts from Soiurce Blog
		switch_to_blog( $from_blog_id );
		$the_query = new WP_Query( $args );
		restore_current_blog();
		
		// Running Posts of Post Type
		while ( $the_query->have_posts() ) : $the_query->the_post();
			global $post;
		
			// Checking if comments have to be copied too
			$copy_attachments = FALSE;
			if ( is_array( $copy_monster_template[ $post_type . '_attachments' ] ) )
				if( in_array( $post->ID, $copy_monster_template[ $post_type . '_attachments' ] ) )
					$copy_attachments = TRUE;
			
			// Checking if comments have to be copied too
			$copy_meta = FALSE;
			if ( is_array( $copy_monster_template[ $post_type . '_meta' ] ) )
				if( in_array( $post->ID, $copy_monster_template[ $post_type . '_meta' ] ) )
					$copy_meta = TRUE;
				
			// Checking if comments have to be copied too
			$copy_comments = FALSE;
			if ( is_array( $copy_monster_template[ $post_type . '_comments' ] ) )
				if( in_array( $post->ID, $copy_monster_template[ $post_type . '_comments' ] ) )
					$copy_comments = TRUE;
			
			// Copy post
			copy_monster_copy_post( $post->ID, $from_blog_id, $to_blog_id, array( 'copy_attachments' => $copy_attachments, 'copy_comments' => $copy_comments, 'copy_meta' => $copy_meta, 'taxonomy_termlist' => $taxonomy_termlist ) );
		endwhile;
		
	endforeach;
}

function copy_monster_copy_post( $post_id, $from_blog_id, $to_blog_id, $args = array() ){
	global $copy_monster_post_relations;
	
	// Setting Arguments
	$defaults = array(
		'copy_attachments' => TRUE,
		'copy_comments' => TRUE,
		'copy_meta' => TRUE,
		'taxonomy_termlist' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	// Getting Data from source Blog
	switch_to_blog( $from_blog_id );
	$post = (array) get_post( $post_id );
	if ( !is_array( $post ) )
		return FALSE;
	
	restore_current_blog();
	
	// Putting Data to destination Blog
	switch_to_blog( $to_blog_id );
	unset( $post[ 'ID' ] ); // Deleting ID for adding new post
	$new_post_id = wp_insert_post( $post );
	
	$copy_monster_post_relations[ $post_id ] = $new_post_id;
	
	if( $new_post_id == 0 )
		return FALSE;
	
	restore_current_blog();
	
	// Setting taxonomies for post
	if( is_array( $taxonomy_termlist ) && count( $taxonomy_termlist ) > 0 ):
		
		foreach ( $taxonomy_termlist AS $taxonomy_name => $taxonomy ):
			
			// Getting Terms of Post
			switch_to_blog( $from_blog_id );
			$post_terms = get_the_terms( $post_id, $taxonomy_name );
			restore_current_blog();
			
			if( is_array( $post_terms ) ):
				foreach( $post_terms AS $post_term ):
					// If its no error entry
					if( is_array( $taxonomy[ $post_term->term_id ] ) ):
						switch_to_blog( $to_blog_id );
						wp_set_object_terms( (int) $new_post_id, (int) $taxonomy[ $post_term->term_id ][ 'term_id' ], $taxonomy_name );
						restore_current_blog();
					endif;
				endforeach;
			endif;
		endforeach;
	endif;
	
	// Copy Attachments
	if( $copy_attachments )
		copy_monster_copy_attachments( $post_id, $new_post_id, $from_blog_id, $to_blog_id );

	// Copy Comments
	if( $copy_comments )
		copy_monster_copy_comments( $post_id, $new_post_id, $from_blog_id, $to_blog_id );
	
	// Copy Meta Data	
	if( $copy_meta )
		copy_monster_copy_meta( $post_id, $new_post_id, $from_blog_id, $to_blog_id );
	
	return $new_post_id;
}

function copy_monster_copy_attachments( $post_id, $new_post_id, $from_blog_id, $to_blog_id ){
	global $copy_monster_post_attachment_relations;
	
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => null,
		'post_status' => null,
		'post_parent' => $post_id
	); 
	
	switch_to_blog( $from_blog_id );
	$attachments = get_posts( $args );
	restore_current_blog();
	
	if( $attachments ):
		foreach( $attachments as $attachment ):
			$attachment = (array) $attachment;
			
			// Getting Attachment data
			switch_to_blog( $from_blog_id );
			$attachment_id = $attachment[ 'ID' ];
			$attachment_meta = get_post_custom( $attachment[ 'ID' ] );
			$attachment_url = wp_get_attachment_url( $attachment_id );
			
			$filename = $attachment_meta[ '_wp_attached_file' ][ 0 ];
			$wp_upload_dir = wp_upload_dir();
			$filepath = $wp_upload_dir[ 'path' ] . '/' . _wp_relative_upload_path( $filename );
			$fileurl =  $attachment[ 'guid' ];
			restore_current_blog();
			
			// Adding Attachment
			switch_to_blog( $to_blog_id );

			unset( $attachment[ 'ID' ] ); // Not needed
			unset( $attachment[ 'post_parent' ] ); // Not needed
			
			$new_wp_upload_dir = wp_upload_dir();
			$new_filepath = $new_wp_upload_dir[ 'path' ] . '/' .  _wp_relative_upload_path( $filename );
			$new_fileurl = $new_wp_upload_dir[ 'baseurl' ] . '/' .  _wp_relative_upload_path( $filename );
			$attachment[ 'guid' ] = $new_fileurl;
			
			$new_attachment_id = wp_insert_attachment( $attachment, $filename, $new_post_id ); // Inserting Attachments
			$new_attachment_url = wp_get_attachment_url( $new_attachment_id );
			copy_monster_copy_meta( $attachment_id, $new_attachment_id, $from_blog_id, $to_blog_id ); // Copy Meta data
			
			$copy_monster_post_attachment_relations[ $attachment_id ] = $new_attachment_id;
			
			// Copy files
			if( file_exists( $filepath ) )
				if( !copy( $filepath , $new_filepath ) )
					wp_die( __( 'Could not copy files from Template. Please deselect to copy attachements or contact your Administrator and try again.', 'copy-monster-options' ) );
			
			// Copy different image sizes
			$attachment_images = unserialize( $attachment_meta[ '_wp_attachment_metadata' ][ 0 ] ) ;
			$attachment_images_sizes = $attachment_images[ 'sizes' ];
			
			$replace_image_sizes = array();
			
			if( is_array( $attachment_images_sizes ) ):
				foreach( $attachment_images_sizes AS $images ):
					// Setting path and url
					$image_filepath = $wp_upload_dir[ 'path' ] . '/' .  _wp_relative_upload_path( $images[ 'file' ] );
					$image_fileurl =  $wp_upload_dir[ 'baseurl' ] . '/' .  _wp_relative_upload_path( $images[ 'file' ] );
					
					$new_image_filepath = $new_wp_upload_dir[ 'path' ] . '/' .  _wp_relative_upload_path( $images[ 'file' ] );
					$new_image_fileurl = $new_wp_upload_dir[ 'baseurl' ] . '/' .  _wp_relative_upload_path( $images[ 'file' ] );
					
					// Adding URLs for replacing
					$replace_files[] = array(
						'url' => $image_fileurl,
						'new_url' => $new_image_fileurl
					);
					
					// Copy file
					if( file_exists( $image_filepath ) ):
						if( !copy( $image_filepath , $new_image_filepath ) ):
							wp_die( __( 'Could not copy files from Template. Please deselect to copy attachements or contact your Administrator and try again.', 'copy-monster-options' ) );
						endif;
					endif;
						
				endforeach;
			endif;
			
			// Getting Post and rewriting attachment URLs
			$new_attachment = (array) get_post( $new_attachment_id );
			$post = (array) get_post( $new_post_id );
			
			// Attachement URL
			$replace_files[] = array(
				'url' => $attachment_url,
				'new_url' => $new_attachment_url
			);
			
			// Attachment href
			$replace_files[] = array(
				'url' => $fileurl,
				'new_url' => $new_fileurl
			);
			
			// Replacing all URLs
			foreach( $replace_files AS $file )
				$post[ 'post_content' ] = str_replace( $file[ 'url' ], $file[ 'new_url' ], $post[ 'post_content' ] );
			
			wp_update_post( $post );
			
			restore_current_blog();
			
		endforeach;
	endif;
}

function copy_monster_copy_comments(  $post_id, $new_post_id, $from_blog_id, $to_blog_id ){
	// Getting Comments
	$args = array(
		'post_id' => $post_id
	);
	
	switch_to_blog( $from_blog_id );
	$comments =  get_comments( $args );
	restore_current_blog();
	
	// Adding Comments
	switch_to_blog( $to_blog_id );
	foreach( $comments as $comment ) :
		$comment = (array) $comment; // Adding needs array
		unset( $comment[ 'comment_ID' ] ); // Dont need it
		
		$comment[ 'comment_post_ID' ] = $new_post_id;
		$comment_id = wp_insert_comment( $comment );
	endforeach;
	restore_current_blog();
}

function copy_monster_copy_meta( $post_id, $new_post_id, $from_blog_id, $to_blog_id ){
	global $copy_monster_post_attachment_relations;
	
	switch_to_blog( $from_blog_id );
	$custom_fields = get_post_custom( $post_id );
	restore_current_blog();
	
	switch_to_blog( $to_blog_id );
	foreach ( $custom_fields AS $custom_field_key => $custom_field ):
		foreach ( $custom_field AS $meta_value ):
			
			// Rewriting Thumbnail ID
			if( '_thumbnail_id' ==  $custom_field_key )
				$meta_value = $copy_monster_post_attachment_relations[ $meta_value ];
			
			add_post_meta( $new_post_id, $custom_field_key, $meta_value );
		endforeach;
	endforeach;
	restore_current_blog();
}

function copy_monster_copy_taxonomies( $post_type, $from_blog_id, $to_blog_id ){
	switch_to_blog( $from_blog_id );
	$taxonomies = get_taxonomies( array( 'object_type' => array( $post_type ) ), 'objects' );
	restore_current_blog();
	
	// Taxonomies
	foreach( $taxonomies AS $taxonomy ):
		$new_termlist[ $taxonomy->name ] = copy_monster_copy_taxonomy( $taxonomy->name, $post_type, $from_blog_id, $to_blog_id );
	endforeach;
	
	return $new_termlist;
}

function copy_monster_copy_taxonomy( $taxonomy_name, $post_type, $from_blog_id, $to_blog_id, $args = array() ){
	global $copy_monster_template;
	
	// Setting Arguments
	$defaults = array(
		'term_ids' => $copy_monster_template[ $post_type . '_taxonomies' ][ $taxonomy_name ]
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	
	switch_to_blog( $from_blog_id );
	$terms = get_terms( $taxonomy_name, array( 'hide_empty' => FALSE ) );
	restore_current_blog();
	
	if( count( $terms ) > 0 && is_array( $terms ) ):
		foreach( $terms AS $term ):
			if( is_array( $term_ids ) )
				if( in_array( $term->term_id, $term_ids ) ):
					$new_terms[ $term->term_id ] = copy_monster_copy_term( $term->term_id, $taxonomy_name, $from_blog_id, $to_blog_id );
				endif;
		endforeach;
	endif;
	
	return $new_terms; 
}

function copy_monster_copy_term( $term_id, $taxonomy_name, $from_blog_id, $to_blog_id ){
	global $copy_monster_term_relations;
	
	switch_to_blog( $from_blog_id );
	$term = (array) get_term_by( 'id', $term_id, $taxonomy_name );
	restore_current_blog();
	
	switch_to_blog( $to_blog_id );
	unset( $term[ 'term_id' ] );
	$new_term = wp_insert_term( $term[ 'name' ], $taxonomy_name, $term );
	restore_current_blog();
	
	// Return have to be Array, else it's an error
	if( is_object( $new_term ) )
		return FALSE;
	
	$copy_monster_term_relations[ $term_id ] = $new_term[ 'term_id' ];
	
	return $new_term;
}

function copy_monster_posts_ignore( $post_types ){
	unset( $post_types[ 'attachment' ] );
	unset( $post_types[ 'revision' ] );
	unset( $post_types[ 'nav_menu_item' ] );
	
	return $post_types;
}
add_filter( 'copy_monster_post_types', 'copy_monster_posts_ignore' );

function copy_monster_posts_save( $input ){
	return $input;
}
/*
 * Getting all used Custom Post Types from existing blog
 */
function copy_monster_get_post_types_db( $wp_post_types_array = FALSE ){
	global $wpdb;
	
	$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT post_type FROM ' . $wpdb->prefix . 'posts' ) );
	
	$post_types = array();
	
	foreach( $rows as $row ):
		if( array_key_exists( $row->post_type, $wp_post_types_array ) ):
			$post_types[ $row->post_type ] = $wp_post_types_array[ $row->post_type ];
		else:
			$post_types[ $row->post_type ] = new stdClass;
			$post_types[ $row->post_type ]->name = $row->post_type;
			$post_types[ $row->post_type ]->label = $row->post_type;
		endif;
	endforeach;
	
	return $post_types;
}

/*
 * Getting all used Custom Post Types from existing blog
 */
function copy_monster_get_taxonomies_db( $post_type ){
	global $wpdb;
	
	// Exit id post type is empty
	if( '' == $post_type )
		return;
		
	// Getting posts of post type
	$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_type="' . $post_type . '"' ) );
	
	foreach( $rows as $row )
		$post_ids[] = $row->ID;
	
	// Exit if no posts where found for post type
	if( 0 == count( $post_ids ) )
		return;
	
	// Creating SQL statement to get taxonomy ids
	$sql = 'SELECT DISTINCT term_taxonomy_id FROM ' . $wpdb->prefix . 'term_relationships WHERE ';
	
	$i = 0;
	
	foreach( $post_ids AS $post_id ):
		if( 0 == $i ):
			$sql.= ' object_id="' . $post_id . '"';
		else:
			$sql.= ' OR object_id="' . $post_id . '"';
		endif;
		$i++;
	endforeach;
	
	$rows = $wpdb->get_results( $wpdb->prepare( $sql ) );
	
	foreach( $rows as $row )
		$term_taxonomy_ids[] = $row->term_taxonomy_id;
	
	// Exit if no taxonomies where found for post type
	if( 0 == count( $term_taxonomy_ids ) )
		return;
	
	// Creating SQL statement to get taxonomies
	$sql = 'SELECT term_taxonomy_id, taxonomy FROM ' . $wpdb->prefix . 'term_taxonomy WHERE ';
	
	$i = 0;
	
	foreach( $term_taxonomy_ids AS $term_taxonomy_id ):
		if( 0 == $i ):
			$sql.= ' term_taxonomy_id="' . $term_taxonomy_id . '"';
		else:
			$sql.= ' OR term_taxonomy_id="' . $term_taxonomy_id . '"';
		endif;
		$i++;
	endforeach;
	
	$rows = $wpdb->get_results( $wpdb->prepare( $sql ) );
	
	foreach( $rows as $row ):
		$taxonomy = new stdClass;
		$taxonomy->name = $row->taxonomy;
		$taxonomy->labels->name = $row->taxonomy;
		$taxonomies[] = $taxonomy;
	endforeach;
	
	return $taxonomies;
}

function copy_monster_get_terms_db( $taxonomy ){
	global $wpdb;
	
	// Exit if taxonomy is empty
	if( '' == $taxonomy )
		return;
		
	// Getting posts of post type
	$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT term_id FROM ' . $wpdb->prefix . 'term_taxonomy WHERE taxonomy="' . $taxonomy . '"' ) );
	
	foreach( $rows as $row )
		$term_ids[] = $row->term_id;
	
	// Exit if no terms where found for taxonomy
	if( 0 == count( $term_ids ) )
		return;
	
	// Creating SQL statement to get taxonomies
	$sql = 'SELECT term_id, name, slug FROM ' . $wpdb->prefix . 'terms WHERE ';
	
	foreach( $term_ids AS $term_id ):
		if( 0 == $i ):
			$sql.= ' term_id="' . $term_id . '"';
		else:
			$sql.= ' OR term_id="' . $term_id . '"';
		endif;
		$i++;
	endforeach;
	
	$rows = $wpdb->get_results( $wpdb->prepare( $sql ) );
	
	foreach( $rows as $row ):
		$term = new stdClass;
		$term->term_id = $row->term_id;
		$term->name = $row->slug;
		$taxonomy->labels->name = $row->name;
		$terms[] = $term;
	endforeach;
	
	return $terms;
}

