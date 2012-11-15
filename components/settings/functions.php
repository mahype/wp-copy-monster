<?php

add_cm_plugin( 'settings', __( 'Settings', 'copy-monster-options' ), 'copy_monster_settings_admin', 'copy_monster_settings_copy', 'copy_monster_settings_save', 25 );

function copy_monster_settings_admin(){ 
	global $wpdb, $copy_monster_template;
	
	switch_to_blog( DFB_TEMPLATE_EDIT_BLOG_ID );
	
	$content = '<table class="widefat">';
	
	$content.= '<thead>';
		$content.= '<tr>';
			$content.= '<th>' . __( 'Setting', 'copy-monster-options' ) . '</th>';	
			$content.= '<th>' . __( 'Action', 'copy-monster-options') .'</th>';
			$content.= '<th>' . __( 'Do this', 'copy-monster-options' ) . '</th>';
	    $content.= '</tr>';
	$content.= '</thead>';
	
	$content.= '<tbody>';
		
		// Theme
		
		if( TRUE == $copy_monster_template[ 'appearance' ][ 'theme' ] )
			$checked_appearance_theme = ' checked';
		
		$content.= '<tr>';
			$content.= '<td><label for="appearance[theme]">' . __( 'Theme', 'copy-monster-options' ) . '</label></td>';
			$content.= '<td>' . sprintf( __('Set up Theme and Theme settings of "%s" theme.' ), get_blog_option( DFB_TEMPLATE_EDIT_BLOG_ID ,'current_theme' ) ) . '</td>';
			$content.= '<td><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][appearance][theme]" value="true"' . $checked_appearance_theme . ' /></td>';
		$content.= '<tr>';
		
		// Plugins
		
		if( TRUE == $copy_monster_template[ 'plugins' ][ 'active' ] )
			$checked_plugins_active = ' checked';
		
		$content.= '<tr>';
			$content.= '<td><label for="">' . __( 'Plugins', 'copy-monster-options' ) . '</label></td>';
			$content.= '<td>' . __('Activate all Plugins like in Blog Template.' ) . '</td>';
			$content.= '<td><input type="checkbox" name="' . CM_OPTION_GROUP . '[' . CM_EMPLATE_EDIT_ID . '][plugins][active]" value="true"' . $checked_plugins_active . ' /></td>';
		$content.= '<tr>';
		
	$content.= '</tbody>';
	
	$content.= '</table>';
	
	$content = apply_filters( 'cm-settings-admin', $content );
	
	echo $content;
	
	do_action( 'cm-settings-admin-bottom' );
	
	restore_current_blog();
}

function copy_monster_settings_copy( $from_blog_id, $to_blog_id ){
	global $copy_monster_template;
	
	if( TRUE == $copy_monster_template[ 'appearance' ][ 'theme' ] )
		copy_monster_appearance_copy( $from_blog_id, $to_blog_id );
		
	if( TRUE == $copy_monster_template[ 'plugins' ][ 'active' ] )
		copy_monster_plugins_copy( $from_blog_id, $to_blog_id );
}

function copy_monster_appearance_copy( $from_blog_id, $to_blog_id ){
	
	switch_to_blog( $to_blog_id );
	$new_theme_mods = get_theme_mod( 'nav_menu_locations' );
	restore_current_blog();
		
	switch_to_blog( $from_blog_id );
		$current_template = get_option( 'current_theme' );
		$template = get_option( 'template' );
		$current_stylesheet = get_option( 'stylesheet' );
		$theme_mods = get_option( 'theme_mods_' . $current_stylesheet );
		$theme_mods[ 'nav_menu_locations' ] = $new_theme_mods; // Let them out because this is made in menu copy function before
	restore_current_blog();
	
	switch_to_blog( $to_blog_id );
		update_option( 'current_theme', $current_template );
		update_option( 'template', $template );
		update_option( 'stylesheet', $current_stylesheet );
		update_option( 'theme_mods_' . $current_stylesheet, $theme_mods );
	restore_current_blog();
}
function copy_monster_plugins_copy( $from_blog_id, $to_blog_id ){
	switch_to_blog( $from_blog_id );
		$active_plugins = get_option( 'active_plugins' );
	restore_current_blog();
	
	switch_to_blog( $to_blog_id );
		update_option( 'active_plugins', $active_plugins );
	restore_current_blog();
}
function copy_monster_settings_save( $input ){
	return $input;
}
