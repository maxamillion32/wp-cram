<?php 



// Projects post type
function cram_custom_post_projects() {
	$labels = array(
		'name'               => _x( 'Projects', 'post type general name' ),
		'singular_name'      => _x( 'Project', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'project' ),
		'add_new_item'       => __( 'Add New Project' ),
		'edit_item'          => __( 'Edit Project' ),
		'new_item'           => __( 'New Project' ),
		'all_items'          => __( 'All Projects' ),
		'view_item'          => __( 'View Project' ),
		'search_items'       => __( 'Search Projects' ),
		'not_found'          => __( 'No projects found' ),
		'not_found_in_trash' => __( 'No projects found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Projects'
	);

	$args = array(
		'labels'        => $labels,
		'description'   => 'Stores our projects and project specific data',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'has_archive'   => true,
		'register_meta_box_cb' => 'add_projects_metaboxes'
	);

	register_post_type( 'projects', $args );	
}

add_action( 'init', 'cram_custom_post_projects' );

//now we have to hide the editor from the 'projects' post editor screen, as we only want
//the media uploader to be visible, so we can upload files
//in the future if wp-core separates these two, we can support the media_uploader above
//in the cpt code and then remove 'editor' from what the cpt supports.

function wpcram_hide_projects_editor() {
    global $current_screen;

    if( $current_screen->post_type == 'projects' ) {
        $css = '<style type="text/css">';
            $css .= '#wp-content-editor-container, #post-status-info, .wp-switch-editor { display: none; }';
        $css .= '</style>';

        echo $css;
    }
}
add_action('admin_footer', 'wpcram_hide_projects_editor');


//updating various messages across wp

function updated_projects_messages( $messages ) {
	global $post, $post_ID;
	$messages['project'] = array(
		0 => '', 
		1 => sprintf( __('Project updated. <a href="%s">View project</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Project updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Project restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Project created. <a href="%s">View project</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Project saved.'),
		8 => sprintf( __('Project submitted. <a target="_blank" href="%s">Preview Project</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Project Creation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview project</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Project draft updated. <a target="_blank" href="%s">Preview project</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}

add_filter( 'post_updated_messages', 'updated_projects_messages' );


function projects_contextual_help( $contextual_help, $screen_id, $screen ) { 
	if ( 'project' == $screen->id ) {
		$contextual_help = '<h2>Projects</h2>
		<p>Projects show the details of the projects that we work on. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
		<p>You can view/edit the details of each project by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple projects.</p>';
	} elseif ( 'edit-project' == $screen->id ) {
		$contextual_help = '<h2>Editing Projects</h2>
		<p>This page allows you to view/modify project details. Please make sure to fill out the available boxes with the appropriate details and <strong>not</strong> add these details to the project description.</p>';
	}
	return $contextual_help;
}

add_action( 'contextual_help', 'projects_contextual_help', 10, 3 );


// Creating Project Types i.e. Web Development, Web Design, Social Media, etc.

function my_project_types() {
	$labels = array(
		'name'              => _x( 'Project Types', 'taxonomy general name' ),
		'singular_name'     => _x( 'Project Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Project Types' ),
		'all_items'         => __( 'All Project Types' ),
		'parent_item'       => __( 'Parent Project Type' ),
		'parent_item_colon' => __( 'Parent Project Type:' ),
		'edit_item'         => __( 'Edit Project Type' ), 
		'update_item'       => __( 'Update Project Type' ),
		'add_new_item'      => __( 'Add New Project Type' ),
		'new_item_name'     => __( 'New Project Type' ),
		'menu_name'         => __( 'Project Types' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);

	register_taxonomy( 'project_types', 'projects', $args );
}

add_action( 'init', 'my_project_types', 0 );


// Creating Project Status via 'project-status' hierarchial taxonomy

function cram_project_statuses() {
	$labels = array(
		'name'              => _x( 'Project Statuses', 'taxonomy general name' ),
		'singular_name'     => _x( 'Project Status', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Project Statuses' ),
		'all_items'         => __( 'All Project Statuses' ),
		'parent_item'       => __( 'Parent Project Status' ),
		'parent_item_colon' => __( 'Parent Project Status:' ),
		'edit_item'         => __( 'Edit Project Status' ), 
		'update_item'       => __( 'Update Project Status' ),
		'add_new_item'      => __( 'Add New Project Status' ),
		'new_item_name'     => __( 'New Project Status' ),
		'menu_name'         => __( 'Project Statuses' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);

	register_taxonomy( 'project_statuses', 'projects', $args );
}

add_action( 'init', 'cram_project_statuses', 0 );

//now we're going to add pre-determined project statuses to the 'project_statuses' taxo
function cram_default_project_statuses() {
	wp_insert_term(
		'Active Project',
		'project_statuses',
		array(
		  'description'	=> 'This is an active project, currently IN PROGRESS.',
		  'slug' 		=> 'active-project'
		)
	);
	wp_insert_term(
		'Pending Project',
		'project_statuses',
		array(
		  'description'	=> 'This project is currently INACTIVE AND PENDING.',
		  'slug' 		=> 'pending-project'
		)
	);
	wp_insert_term(
		'Internal Project Review',
		'project_statuses',
		array(
		  'description'	=> 'This project is currently being REVIEWED INTERNALLY.  Please see project comments for more information.',
		  'slug' 		=> 'internal-project-review'
		)
	);
	wp_insert_term(
		'Client Reviewing Project',
		'project_statuses',
		array(
		  'description'	=> 'This project is currently being REVIEWED BY CLIENT.  Please see project comments for more information.',
		  'slug' 		=> 'client-reviewing-project'
		)
	);
	wp_insert_term(
		'Project Completed',
		'project_statuses',
		array(
		  'description'	=> 'This project has been reviewed and COMPLETED.  NO WORK NEEDED.',
		  'slug' 		=> 'completed-project'
		)
	);	
}
add_action( 'init', 'cram_default_project_statuses' );


//add project details metabox
//note that the add_action is not needed as we registered the metabox during custom post type creation above

function add_projects_metaboxes() {
	// project details metabox for project details
    add_meta_box( 'cram_project_details_metabox', 'Project Details',
    	'cram_project_details_metabox_meta_callback', 'projects', 'normal', 'high' );
}

//project details meta callback function
function cram_project_details_metabox_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'cram_project_nonce' );
    $cram_projects_stored_meta = get_post_meta( $post->ID );
    ?>

<!-- adding the fields and content to the metabox -->
	<h2>Project Kickoff & Project Deadline</h2>
    <p>
        <label for="cram-projects-start-date" class="cram-projects-start-date-title"><strong>Start Date</strong></label>
        <input type="text" name="cram-projects-start-date-entry" id="cram_projects_start_date" class="datepicker-field" value="<?php echo $cram_projects_stored_meta['cram-projects-start-date-entry'][0]; ?>" />
    </p>
    <p>
        <label for="cram-projects-deadline" class="cram-projects-deadline-title"><strong>Project Deadline</strong></label>
        <input type="text" name="cram-projects-deadline-entry" id="cram_projects_deadline" class="datepicker-field" value="<?php echo $cram_projects_stored_meta['cram-projects-deadline-entry'][0]; ?>" />
    </p>
    <h2>Project Scope</h2>
    <p>
        <textarea name="cram-projects-scope-entry" id="cramprojectsscope" rows="6" style="width:99%"><?php echo $cram_projects_stored_meta['cram-projects-scope-entry'][0]; ?></textarea>
    </p>
    <h2>Project Credentials</h2>
	<p>
		<textarea name="cram-projects-credentials-entry" id="cramprojectscredentials" rows="6" style="width:99%"><?php echo $cram_projects_stored_meta['cram-projects-credentials-entry'][0]; ?></textarea>
	</p>
	<h2>Hours & Billing</h2>
	<p>
		<label for="cram-projects-total-hours" class="cram-projects-total-hours-title"><strong>Total Hours</strong></label>
		<input type="text" name="cram-projects-total-hours-entry" id="cram_projects_total_hours" value="<?php echo $cram_projects_stored_meta['cram-projects-total-hours-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-projects-hours-billed" class="cram-projects-hours-billed-title"><strong>Hours Billed</strong></label>
		<input type="text" name="cram-projects-hours-billed-entry" id="cram_projects_hours_billed" value="<?php echo $cram_projects_stored_meta['cram-projects-hours-billed-entry'][0]; ?>">
	</p>

	<p>
    <?php
} // end cram_projects_stored_meta()


//saving the custom post meta
function cram_projects_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'cram_projects_nonce' ] ) && wp_verify_nonce( $_POST[ 'cram_projects_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'cram-projects-start-date-entry' ] ) ) {
        update_post_meta( $post_id, 'cram-projects-start-date-entry', sanitize_text_field( $_POST[ 'cram-projects-start-date-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-projects-deadline-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-projects-deadline-entry', sanitize_text_field( $_POST[ 'cram-projects-deadline-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-projects-scope-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-projects-scope-entry', $_POST[ 'cram-projects-scope-entry' ] );
    }

    if( isset( $_POST[ 'cram-projects-credentials-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-projects-credentials-entry', $_POST[ 'cram-projects-credentials-entry' ] );
    }

    if( isset( $_POST[ 'cram-projects-total-hours-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-projects-total-hours-entry', sanitize_text_field( $_POST[ 'cram-projects-total-hours-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-projects-hours-billed-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-projects-hours-billed-entry', sanitize_text_field( $_POST[ 'cram-projects-hours-billed-entry' ] ) );
    }
 
} // end example_meta_save()
add_action( 'save_post', 'cram_projects_meta_save' );    

//add wp-editor (minus the media uploader) to needed custom meta fields
//had to remove the wp_editor additions to textareas, as it was stripping html and adding duplicate wp_editor instances throughout
//the entire site in the footer of wp-admin areas.
//will revisit this for beta release
/*function cram_add_projects_editors() {
	wp_editor( $content, 'cramprojectsscope', $settings = array( 'media_buttons' => false,  ) );
	wp_editor( $content, 'cramprojectscredentials', $settings = array( 'media_buttons' => false, ) );
}
add_action( 'admin_print_footer_scripts', 'cram_add_projects_editors');*/

?>