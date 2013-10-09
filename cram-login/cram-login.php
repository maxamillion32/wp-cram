<?php 

//enqueue necessary scripts
function custom_login_css() {
    wp_enqueue_style('new-login-css', plugin_dir_url( __FILE__ ) . '/login-styles.css' );
}

add_action('login_head', 'custom_login_css');

//wp login modifications
/***attach custom wp-login CSS file***/

add_action( 'login_head', 'cram_fadein',30);

//jQuery fadein for little aesthetic touch on login screen
function cram_fadein() {
	echo '<script type="text/javascript">// <![CDATA[
	jQuery(document).ready(function() { 
		jQuery("#loginform,#nav,#backtoblog").css("display", "none");
		jQuery("#loginform,#nav,#backtoblog").fadeIn(4500);     
	});
	// ]]></script>';
}

//swap out link to wordpress.org that is native to the headerurl image on wp-login.php
add_filter( 'login_headerurl', 'cram_login_header_url' );
	function cram_login_header_url($url) {
		return 'home_url()';
}


?>