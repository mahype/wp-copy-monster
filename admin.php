<?php

global $cm_templates;

$plugins = get_cm_plugins();

$elements = array();

tk_form_start( 'copy-monster-config' );

if( is_array( $plugins ) ):
	foreach( $plugins AS $plugin ):
			
		// If Templates are added and Template ID is set OR tab is blog template settings
		if( ( CM_EMPLATE_EDIT_ID > 0  || 'blog-template' == $plugin['slug'] ) && function_exists( $plugin[ 'function_admin' ] ) ):
		
			$element[ 'id' ] = $plugin['slug'];
			$element[ 'title' ] = $plugin['title'];
			
			ob_start();
			if( function_exists( $plugin[ 'function_admin' ] ) ) call_user_func( $plugin[ 'function_admin' ] );
			$element[ 'content' ] = ob_get_clean();
			
			$elements[] = $element;
		endif;
			
	endforeach;
endif;

echo tk_tabs( 'copy_monster_tabs', $elements, 'html' );

echo tk_form_button( __( 'Save Settings', 'copy-monster-options' ) );

$content = tk_form_end( FALSE );

?>
<div class="wrap">
    <h2><?php _e( 'Copy Monster', 'copy-monster-options' ); ?></h2>
    <p><?php _e( 'Create your Blog Template and setup which things you want to copy to new created blogs.', 'copy-monster-options' ); ?></p>
    <?php echo $content; ?>
    
</div>