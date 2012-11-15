<?php
/*
 * Plugin functions
 */

function add_cm_plugin( $slug, $title, $function_admin, $function_copy = '', $function_save = '', $priority = 10 ){
	$plugins = get_option( CM_PLUGIN_OPTIONS );
	
	$plugin_slugs = get_cm_plugin_slugs();
	
	if( is_array( $plugin_slugs ) ):
		// Deleting plugins with same slug, which have been added before
		if( in_array( $slug, $plugin_slugs ) ):
			foreach( $plugins AS $key => $plugin_list ):
				if( array_key_exists( $slug, $plugin_list ) ):
					unset( $plugins[ $key ][ $slug ] );
				endif;
			endforeach;
		endif;
	endif;
	
	//Setting up new data
	$plugin[ 'slug' ] = $slug;
	$plugin[ 'title' ] = $title;
	$plugin[ 'function_admin' ] = $function_admin;
	$plugin[ 'function_save' ] = $function_save;
	$plugin[ 'function_copy' ] = $function_copy;
	
	$plugins[ $priority ][ $slug ] = $plugin;
	
	ksort( $plugins );
	
	update_option( CM_PLUGIN_OPTIONS, $plugins );
}

function get_cm_plugins(){
	$plugins = get_option( CM_PLUGIN_OPTIONS );
	
	// Sorting Array because of priority numbers
	if( is_array( $plugins ) )
		ksort( $plugins );
	
	$all_plugins = array();
	
	if( !is_array( $plugins ) )
		return FALSE;
	
	foreach( $plugins AS $priority ):
		foreach( $priority AS $plugin ):
			$all_plugins[ $plugin[ 'slug' ] ] = $plugin;
		endforeach;
	endforeach;
	
	return $all_plugins;
}

function get_cm_plugin_slugs(){
	$plugins = get_cm_plugins();
	
	$plugin_slugs = array();
	
	if( !is_array( $plugins ) )
		return FALSE;
	
	foreach( $plugins AS $plugin ):
		$plugin_slugs[] = $plugin[ 'slug' ];
	endforeach;
			
	return $plugin_slugs;
}

function get_cm_plugin( $slug ){
	$plugins = get_cm_plugins();
	
	foreach( $plugins AS $plugin ):
		if( $slug == $plugin[ 'slug' ] )
			return $plugin;
	endforeach;
	
}