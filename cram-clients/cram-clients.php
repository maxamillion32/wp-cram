<?php 


function cram_custom_post_client() {
	$labels = array(
		'name'               => _x( 'Clients', 'post type general name' ),
		'singular_name'      => _x( 'Client', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'client' ),
		'add_new_item'       => __( 'Add New Client' ),
		'edit_item'          => __( 'Edit Client' ),
		'new_item'           => __( 'New Client' ),
		'all_items'          => __( 'All Clients' ),
		'view_item'          => __( 'View Client' ),
		'search_items'       => __( 'Search Clients' ),
		'not_found'          => __( 'No clients found' ),
		'not_found_in_trash' => __( 'No clients found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Clients'
	);

	$args = array(
		'labels'        => $labels,
		'description'   => 'Stores our clients and client specific data',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'comments' ),
		'has_archive'   => true,
		'register_meta_box_cb' => 'add_clients_metaboxes'
	);

	register_post_type( 'clients', $args );	
}

add_action( 'init', 'cram_custom_post_client' );


//now we have to hide the editor from the 'client' post editor screen, as we only want
//the media uploader to be visible, so we can upload files
//in the future if wp-core separates these two, we can support the media_uploader above
//in the cpt code and then remove 'editor' from what the cpt supports.

function wpcram_hide_clients_editor() {
    global $current_screen;

    if( $current_screen->post_type == 'clients' ) {
        $css = '<style type="text/css">';
            $css .= '#wp-content-editor-container, #post-status-info, .wp-switch-editor { display: none; }';
        $css .= '</style>';

        echo $css;
    }
}
add_action('admin_footer', 'wpcram_hide_clients_editor');



//updating various messages across wp

function update_client_messages( $messages ) {
	global $post, $post_ID;
	$messages['client'] = array(
		0 => '', 
		1 => sprintf( __('Client updated. <a href="%s">View client</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Client updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Client restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Client created. <a href="%s">View client</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Client saved.'),
		8 => sprintf( __('Client submitted. <a target="_blank" href="%s">Preview Client</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Client Creation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview client</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Client draft updated. <a target="_blank" href="%s">Preview client</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}

add_filter( 'post_updated_messages', 'update_client_messages' );


function clients_contextual_help( $contextual_help, $screen_id, $screen ) { 
	if ( 'client' == $screen->id ) {
		$contextual_help = '<h2>Clients</h2>
		<p>Clients show the details of the clients that we work with. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
		<p>You can view/edit the details of each client by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple clients.</p>';
	} elseif ( 'edit-client' == $screen->id ) {
		$contextual_help = '<h2>Editing Clients</h2>
		<p>This page allows you to view/modify client details. Please make sure to fill out the available boxes with the appropriate details and <strong>not</strong> add these details to the client description.</p>';
	}
	return $contextual_help;
}

add_action( 'contextual_help', 'clients_contextual_help', 10, 3 );


// Adding Client Types
function cram_client_types() {
	$labels = array(
		'name'              => _x( 'Client Types', 'taxonomy general name' ),
		'singular_name'     => _x( 'Client Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Client Types' ),
		'all_items'         => __( 'All Client Types' ),
		'parent_item'       => __( 'Parent Client Type' ),
		'parent_item_colon' => __( 'Parent Client Type:' ),
		'edit_item'         => __( 'Edit Client Type' ), 
		'update_item'       => __( 'Update Client Type' ),
		'add_new_item'      => __( 'Add New Client Type' ),
		'new_item_name'     => __( 'New Client Type' ),
		'menu_name'         => __( 'Client Types' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);

	register_taxonomy( 'client_types', 'clients', $args );
}

add_action( 'init', 'cram_client_types', 0 );

/**************************************************************************/
/**************************************************************************/
/*                                                                        */
/*                       Meta Boxes & Custom Meta                         */
/*                                                                        */
/**************************************************************************/
/**************************************************************************/

// Creating Client Status via 'client-status' hierarchial taxonomy

function cram_client_statuses() {
	$labels = array(
		'name'              => _x( 'Client Statuses', 'taxonomy general name' ),
		'singular_name'     => _x( 'Client Status', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Client Statuses' ),
		'all_items'         => __( 'All Client Statuses' ),
		'parent_item'       => __( 'Parent Client Status' ),
		'parent_item_colon' => __( 'Parent Client Status:' ),
		'edit_item'         => __( 'Edit Client Status' ), 
		'update_item'       => __( 'Update Client Status' ),
		'add_new_item'      => __( 'Add New Client Status' ),
		'new_item_name'     => __( 'New Client Status' ),
		'menu_name'         => __( 'Client Statuses' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);

	register_taxonomy( 'client_statuses', 'clients', $args );
}

add_action( 'init', 'cram_client_statuses', 0 );

//now we're going to add pre-determined Client Statuses to the 'client_statuses' taxo
function cram_default_client_statuses() {
	wp_insert_term(
		'Active Client',
		'client_statuses',
		array(
		  'description'	=> 'Currently working with this client on REGULAR BASIS.',
		  'slug' 		=> 'active-client'
		)
	);
	wp_insert_term(
		'Prospective Client',
		'client_statuses',
		array(
		  'description'	=> 'This is a POTENTIAL CLIENT and no projects or tasks have been started.',
		  'slug' 		=> 'prospective-client'
		)
	);
	wp_insert_term(
		'Average Client',
		'client_statuses',
		array(
		  'description'	=> 'This is an AVERAGE CLIENT that has projects/tasks at an average rate.',
		  'slug' 		=> 'average-client'
		)
	);
	wp_insert_term(
		'Legacy Client',
		'client_statuses',
		array(
		  'description'	=> 'LONGSTANDING CLIENT that you have worked with for a long time.',
		  'slug' 		=> 'legacy-client'
		)
	);
	wp_insert_term(
		'Past Client',
		'client_statuses',
		array(
		  'description'	=> 'This is a client that you NO LONGER DO BUSINESS WITH.  See Client comments/notes for more information.',
		  'slug' 		=> 'past-client'
		)
	);

	wp_insert_term(
		'Referral Client',
		'client_statuses',
		array(
		  'description'	=> 'This is a client that REFERS OTHER CLIENTS TO YOU.  See Client comments/notes and REFERRALS for more information.',
		  'slug' 		=> 'referal-client'
		)
	);
	wp_insert_term(
		'Referred Client',
		'client_statuses',
		array(
		  'description'	=> 'This is a client that WAS REFERRED TO YOU BY ANOTHER CLIENT.  See Client comments/notes and REFERER for more information.',
		  'slug' 		=> 'referred-client'
		)
	);			
}
add_action( 'init', 'cram_default_client_statuses' );


//add client details metabox
//note that the add_action is not needed as we registered the metabox during custom post type creation above

function add_clients_metaboxes() {
	// client details metabox for client details
    add_meta_box( 'cram_client_details_metabox', 'Client Details', 
    	'cram_client_details_metabox_meta_callback', 'clients', 'normal', 'high' );
}

//client details meta callback function
function cram_client_details_metabox_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'cram_client_nonce' );
    $cram_clients_stored_meta = get_post_meta( $post->ID );
    ?>

<!-- adding the fields and content to the metabox -->
    <center><h2>Client Details</h2></center>
    <p>
	<h2>Contact Information</h2>
	<h3>Phone Numbers</h3>
	<p>
		<label for="cram-clients-main-phone" class="cram-clients-main-phone-title"><strong>Main Phone</strong></label>
		<input type="text" name="cram-clients-main-phone-entry" id="cram_clients_main_phone" value="<?php echo $cram_clients_stored_meta['cram-clients-main-phone-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-alt-phone" class="cram-clients-alt-phone-title"><strong>Alt Phone</strong></label>
		<input type="text" name="cram-clients-alt-phone-entry" id="cram_clients_alt_phone" value="<?php echo $cram_clients_stored_meta['cram-clients-alt-phone-entry'][0]; ?>">
	</p>
	<h3>Location</h3>
	<p>
		<label for="cram-clients-street-address" class="cram-clients-street-address-title"><strong>Street Address</strong></label>
		<input type="text" name="cram-clients-street-address-entry" id="cram_clients_street_address" value="<?php echo $cram_clients_stored_meta['cram-clients-street-address-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-city" class="cram-clients-city-title"><strong>City</strong></label>
		<input type="text" name="cram-clients-city-entry" id="cram_clients_city" value="<?php echo $cram_clients_stored_meta['cram-clients-city-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-state" class="cram-clients-state-title"><strong>State</strong></label>
		<input type="text" name="cram-clients-state-entry" id="cram_clients_state" value="<?php echo $cram_clients_stored_meta['cram-clients-state-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-zip-code" class="cram-clients-zip-code-title"><strong>Zip Code</strong></label>
		<input type="text" name="cram-clients-zip-code-entry" id="cram_clients_zip_code" value="<?php echo $cram_clients_stored_meta['cram-clients-zip-code-entry'][0]; ?>">
	</p>
	<h3>Website(s)</h3>
	<p>
		<textarea name="cram-clients-website-entry" id="cram_clients_website" rows="4" style="width:99%"><?php echo $cram_clients_stored_meta['cram-clients-website-entry'][0]; ?></textarea>
	</p>
	<h2>Social Profiles</h2>
	<p>
		<label for="cram-clients-gplus" class="cram-clients-gplus-title"><strong>Google+</strong></label>
		<input type="text" name="cram-clients-gplus-entry" id="cram_clients_gplus" value="<?php echo $cram_clients_stored_meta['cram-clients-gplus-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-facebook" class="cram-clients-facebook-title"><strong>Facebook</strong></label>
		<input type="text" name="cram-clients-facebook-entry" id="cram_clients_facebook" value="<?php echo $cram_clients_stored_meta['cram-clients-facebook-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-twitter" class="cram-clients-twitter-title"><strong>Twitter</strong></label>
		<input type="text" name="cram-clients-twitter-entry" id="cram_clients_twitter" value="<?php echo $cram_clients_stored_meta['cram-clients-twitter-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-youtube" class="cram-clients-youtube-title"><strong>YouTube</strong></label>
		<input type="text" name="cram-clients-youtube-entry" id="cram_clients_youtube" value="<?php echo $cram_clients_stored_meta['cram-clients-youtube-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-pinterest" class="cram-clients-pinterest-title"><strong>Pinterest</strong></label>
		<input type="text" name="cram-clients-pinterest-entry" id="cram_clients_pinterest" value="<?php echo $cram_clients_stored_meta['cram-clients-pinterest-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-clients-linkedin" class="cram-clients_linkedin_title"><strong>LinkedIn</strong></label>
		<input type="text" name="cram-clients-linkedin-entry" id="cram_clients_linkedin" value="<?php echo $cram_clients_stored_meta['cram-clients-linkedin-entry'][0]; ?>">
	</p>
    <h2>Client Credentials</h2>
	<p>
		<textarea name="cram-clients-credentials-entry" id="cram_clients_credentials" rows="6" style="width:99%"><?php echo $cram_clients_stored_meta['cram-clients-credentials-entry'][0]; ?></textarea>
	</p>
	<p>

    <?php
} // end cram_clients_stored_meta()


//saving the custom post meta
function cram_clients_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'cram_clients_nonce' ] ) && wp_verify_nonce( $_POST[ 'cram_clients_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'cram-clients-main-phone-entry' ] ) ) {
        update_post_meta( $post_id, 'cram-clients-main-phone-entry', sanitize_text_field( $_POST[ 'cram-clients-main-phone-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-alt-phone-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-alt-phone-entry', sanitize_text_field( $_POST[ 'cram-clients-alt-phone-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-street-address-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-street-address-entry', sanitize_text_field( $_POST[ 'cram-clients-street-address-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-city-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-city-entry', sanitize_text_field( $_POST[ 'cram-clients-city-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-state-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-state-entry', sanitize_text_field( $_POST[ 'cram-clients-state-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-zip-code-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-zip-code-entry', sanitize_text_field( $_POST[ 'cram-clients-zip-code-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-website-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-website-entry', $_POST[ 'cram-clients-website-entry' ] );
    }

    if( isset( $_POST[ 'cram-clients-gplus-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-gplus-entry', sanitize_text_field( $_POST[ 'cram-clients-gplus-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-facebook-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-facebook-entry', sanitize_text_field( $_POST[ 'cram-clients-facebook-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-twitter-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-twitter-entry', sanitize_text_field( $_POST[ 'cram-clients-twitter-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-youtube-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-youtube-entry', sanitize_text_field( $_POST[ 'cram-clients-youtube-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-pinterest-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-pinterest-entry', sanitize_text_field( $_POST[ 'cram-clients-pinterest-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-linkedin-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-linkedin-entry', sanitize_text_field( $_POST[ 'cram-clients-linkedin-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-clients-credentials-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-clients-credentials-entry', $_POST[ 'cram-clients-credentials-entry' ] );
    }     
 
} // end example_meta_save()
add_action( 'save_post', 'cram_clients_meta_save' );

//add wp-editor (minus the media uploader) to needed custom meta fields
//had to remove the wp_editor additions to textareas, as it was stripping html and adding duplicate wp_editor instances throughout
//the entire site in the footer of wp-admin areas.
//will revisit this for beta release
/*function cram_add_clients_editors( $post ) {
	wp_editor( $content, 'cram_clients_website', $settings = array( 'media_buttons' => false ) );
	wp_editor( $content, 'cram_clients_credentials', $settings = array( 'media_buttons' => false ) );
}
add_action( 'admin_footer', 'cram_add_clients_editors');*/



?>