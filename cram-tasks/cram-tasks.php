<?php 


function cram_custom_post_tasks() {
	$labels = array(
		'name'               => _x( 'Tasks', 'post type general name' ),
		'singular_name'      => _x( 'Task', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'task' ),
		'add_new_item'       => __( 'Add New Task' ),
		'edit_item'          => __( 'Edit Task' ),
		'new_item'           => __( 'New Task' ),
		'all_items'          => __( 'All Tasks' ),
		'view_item'          => __( 'View Task' ),
		'search_items'       => __( 'Search Tasks' ),
		'not_found'          => __( 'No tasks found' ),
		'not_found_in_trash' => __( 'No tasks found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Tasks'
	);

	$args = array(
		'labels'        => $labels,
		'description'   => 'Stores our tasks and task specific data',
		'menu_icon'     => WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),'',plugin_basename(__FILE__)).'tasks-red-16.png',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'comments' ),
		'has_archive'   => true,
		'register_meta_box_cb' => 'add_tasks_metaboxes'
	);

	register_post_type( 'tasks', $args );	
}

add_action( 'init', 'cram_custom_post_tasks' );

//now we have to hide the editor from the 'tasks' post editor screen, as we only want
//the media uploader to be visible, so we can upload files
//in the future if wp-core separates these two, we can support the media_uploader above
//in the cpt code and then remove 'editor' from what the cpt supports.

function wpcram_hide_tasks_editor() {
    global $current_screen;

    if( $current_screen->post_type == 'tasks' ) {
        $css = '<style type="text/css">';
            $css .= '#wp-content-editor-container, #post-status-info, .wp-switch-editor { display: none; }';
        $css .= '</style>';

        echo $css;
    }
}
add_action('admin_footer', 'wpcram_hide_tasks_editor');


//updating various messages across wp

function updated_tasks_messages( $messages ) {
	global $post, $post_ID;
	$messages['tasks'] = array(
		0 => '', 
		1 => sprintf( __('Task updated. <a href="%s">View task</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Task updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Task restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Task created. <a href="%s">View Task</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Task saved.'),
		8 => sprintf( __('Task submitted. <a target="_blank" href="%s">Preview Task</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Task Creation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Task</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Task draft updated. <a target="_blank" href="%s">Preview Task</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}

add_filter( 'post_updated_messages', 'updated_tasks_messages' );


function tasks_contextual_help( $contextual_help, $screen_id, $screen ) { 
	if ( 'tasks' == $screen->id ) {
		$contextual_help = '<h2>Tasks</h2>
		<p>Tasks show the details of the different tasks that we work on for clients. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
		<p>You can view/edit the details of each task by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple tasks.</p>';
	} elseif ( 'edit-task' == $screen->id ) {
		$contextual_help = '<h2>Editing Tasks</h2>
		<p>This page allows you to view/modify tasks details. Please make sure to fill out the available boxes with the appropriate details and <strong>not</strong> add these details to the task description.</p>';
	}
	return $contextual_help;
}

add_action( 'contextual_help', 'tasks_contextual_help', 10, 3 );


// Adding Cats to Tasks

function my_tasks_types() {
	$labels = array(
		'name'              => _x( 'Task Types', 'taxonomy general name' ),
		'singular_name'     => _x( 'Task Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Task Types' ),
		'all_items'         => __( 'All Task Types' ),
		'parent_item'       => __( 'Parent Task Type' ),
		'parent_item_colon' => __( 'Parent Task Type:' ),
		'edit_item'         => __( 'Edit Task Type' ), 
		'update_item'       => __( 'Update Task Type' ),
		'add_new_item'      => __( 'Add New Task Type' ),
		'new_item_name'     => __( 'New Task Type' ),
		'menu_name'         => __( 'Task Types' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);

	register_taxonomy( 'tasks_type', 'tasks', $args );
}

add_action( 'init', 'my_tasks_types', 0 );

/**************************************************************************/
/**************************************************************************/
/*                                                                        */
/*                       Meta Boxes & Custom Meta                         */
/*                                                                        */
/**************************************************************************/
/**************************************************************************/

// Creating Task Status via 'task-status' hierarchial taxonomy

function cram_task_statuses() {
	$labels = array(
		'name'              => _x( 'Task Statuses', 'taxonomy general name' ),
		'singular_name'     => _x( 'Task Status', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Task Statuses' ),
		'all_items'         => __( 'All Task Statuses' ),
		'parent_item'       => __( 'Parent Task Status' ),
		'parent_item_colon' => __( 'Parent Task Status:' ),
		'edit_item'         => __( 'Edit Task Status' ), 
		'update_item'       => __( 'Update Task Status' ),
		'add_new_item'      => __( 'Add New Task Status' ),
		'new_item_name'     => __( 'New Task Status' ),
		'menu_name'         => __( 'Task Statuses' ),
	);

	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);

	register_taxonomy( 'task_statuses', 'tasks', $args );
}

add_action( 'init', 'cram_task_statuses', 0 );

//now we're going to add pre-determined tasks statuses to the 'task_statuses' taxo
function cram_default_task_statuses() {
	wp_insert_term(
		'Active Task',
		'task_statuses',
		array(
		  'description'	=> 'This is an active task, currently IN PROGRESS.',
		  'slug' 		=> 'active-task'
		)
	);
	wp_insert_term(
		'Pending Task',
		'task_statuses',
		array(
		  'description'	=> 'This task is currently INACTIVE AND PENDING.',
		  'slug' 		=> 'pending-task'
		)
	);
	wp_insert_term(
		'Internal Task Review',
		'task_statuses',
		array(
		  'description'	=> 'This task is currently being REVIEWED INTERNALLY.  Please see task comments for more information.',
		  'slug' 		=> 'internal-task-review'
		)
	);
	wp_insert_term(
		'Client Reviewing Task',
		'task_statuses',
		array(
		  'description'	=> 'This task is currently being REVIEWED BY CLIENT.  Please see task comments for more information.',
		  'slug' 		=> 'client-reviewing-task'
		)
	);
	wp_insert_term(
		'Task Completed',
		'task_statuses',
		array(
		  'description'	=> 'This task has been reviewed and COMPLETED.  NO WORK NEEDED.',
		  'slug' 		=> 'completed-task'
		)
	);	
}
add_action( 'init', 'cram_default_task_statuses' );


//add task details metabox
//note that the add_action is not needed as we registered the metabox during custom post type creation above

function add_tasks_metaboxes() {
	// task details metabox for task details
    add_meta_box( 'cram_task_details_metabox', 'Task Details', 
    	'cram_task_details_metabox_meta_callback', 'tasks', 'normal', 'high' );
}

//task details meta callback function
function cram_task_details_metabox_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'cram_task_nonce' );
    $cram_tasks_stored_meta = get_post_meta( $post->ID );
    
?>

<!-- adding the fields and content to the metabox -->
    <center><h2>Task Kickoff & Task Deadline</h2></center>
    <p>
        <label for="cram-tasks-start-date" class="cram-tasks-start-date-title"><strong>Task Kickoff</strong></label>
        <input type="text" name="cram-tasks-start-date-entry" id="cram_tasks_start_date" class="datepicker-field" value="<?php echo $cram_tasks_stored_meta['cram-tasks-start-date-entry'][0]; ?>" />
    </p>
    <p>
        <label for="cram-tasks-deadline" class="cram-tasks-deadline-title"><strong>Task Deadline</strong></label>
        <input type="text" name="cram-tasks-deadline-entry" id="cram_tasks_deadline" class="datepicker-field" value="<?php echo $cram_tasks_stored_meta['cram-tasks-deadline-entry'][0]; ?>" />
    </p>
    <h2>Task Scope</h2>
    <p>
        <textarea name="cram-tasks-scope-entry" id="cram_tasks_scope" rows="6" style="width:80%;"><?php echo $cram_tasks_stored_meta['cram-tasks-scope-entry'][0]; ?></textarea>
    </p>
    <h2>Task Credentials</h2>
	<p>
		<textarea name="cram-tasks-credentials-entry" id="cram_tasks_credentials" rows="6" style="width:95%;"><?php echo $cram_tasks_stored_meta['cram-tasks-credentials-entry'][0]; ?></textarea>
	</p>
	<h2>Hours & Billing</h2>
	<p>
		<label for="cram-tasks-total-hours" class="cram-tasks-total-hours-title"><strong>Total Hours</strong></label>
		<input type="text" name="cram-tasks-total-hours-entry" id="cram_tasks_total_hours" value="<?php echo $cram_tasks_stored_meta['cram-tasks-total-hours-entry'][0]; ?>">
	</p>
	<p>
		<label for="cram-tasks-hours-billed" class="cram-tasks-hours-billed-title"><strong>Hours Billed</strong></label>
		<input type="text" name="cram-tasks-hours-billed-entry" id="cram_tasks_hours_billed" value="<?php echo $cram_tasks_stored_meta['cram-tasks-hours-billed-entry'][0]; ?>">
	</p>

	<p>
    <?php
} // end cram_tasks_stored_meta()


//saving the custom post meta
function cram_tasks_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'cram_tasks_nonce' ] ) && wp_verify_nonce( $_POST[ 'cram_tasks_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'cram-tasks-start-date-entry' ] ) ) {
        update_post_meta( $post_id, 'cram-tasks-start-date-entry', sanitize_text_field( $_POST[ 'cram-tasks-start-date-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-tasks-deadline-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-tasks-deadline-entry', sanitize_text_field( $_POST[ 'cram-tasks-deadline-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-tasks-scope-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-tasks-scope-entry', $_POST[ 'cram-tasks-scope-entry' ] );
    }

    if( isset( $_POST[ 'cram-tasks-credentials-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-tasks-credentials-entry', $_POST[ 'cram-tasks-credentials-entry' ] );
    }

    if( isset( $_POST[ 'cram-tasks-total-hours-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-tasks-total-hours-entry', sanitize_text_field( $_POST[ 'cram-tasks-total-hours-entry' ] ) );
    }

    if( isset( $_POST[ 'cram-tasks-hours-billed-entry' ] ) ) {
    	update_post_meta( $post_id, 'cram-tasks-hours-billed-entry', sanitize_text_field( $_POST[ 'cram-tasks-hours-billed-entry' ] ) );
    }
 
} // end cram_tasks_meta_save()
add_action( 'save_post', 'cram_tasks_meta_save' );

//add wp-editor (minus the media uploader) to needed custom meta fields
//had to remove the wp_editor additions to textareas, as it was stripping html and adding duplicate wp_editor instances throughout
//the entire site in the footer of wp-admin areas.
//will revisit this for beta release
/*function cram_add_tasks_editors( $post ) {
	wp_editor( $content, 'cram_tasks_scope', $settings = array( 'media_buttons' => false ) );
	wp_editor( $content, 'cram_tasks_credentials', $settings = array( 'media_buttons' => false ) );
}
add_action( 'admin_footer', 'cram_add_tasks_editors');*/


?>