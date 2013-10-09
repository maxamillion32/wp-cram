<?php

//enqueue necessary scripts
function cram_jumpoff($hook) {
    switch ($hook) {
        case 'edit.php':
        case 'post.php':
        case 'post-new.php':
        	wp_enqueue_script( 'jquery-ui-datepicker', 'jquery' );
            wp_enqueue_script( 'datepicker-fields', plugin_dir_url( __FILE__ ) . '/js/admin.js' );
            wp_enqueue_style('jquery-ui-stylesheet', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
        break;
    }
}
add_action( 'admin_enqueue_scripts', 'cram_jumpoff' );


//
//creating custom roles and capabilities  'client-contact', 'support' and 'agent'
//'client-contact' => a client's contact i.e. Business Owner, Manager, for the 'Client' CPT 
//'support' => WP CR[a]M owner's customer service or support employees to manage the 'client' CPT
//'Task Manager' => the WP CR[a]M owner's person who facilitate's [manages] a customer's task i.e. Web Designer, Web Developer, Sales Rep, etc.
//'Project Manager' => the WP CR[a]M owner's person who manages a client project (very similar to a Task Manager, but 'Projects' are larger and can include their own 'Tasks)
//

add_role('client-contact', 'Client Contact', array(
    'read' => true, 
    'edit_posts' => false,
    'delete_posts' => false, 
));

add_role('support', 'Support', array(
	'read' => true,
	'edit_posts' => true,
	'edit_others_posts' => true,
	'delete_posts' => false,
));

add_role('task-manager', 'Task Manager', array(
	'read' => true,
	'edit_posts' => true,
	'edit_others_posts' => true,
	'delete_posts' => false,
	'delete_others_posts' => false,
));

add_role('project-manager', 'Project Manager', array(
	'read' => true,
	'edit_posts' => true,
	'edit_others_posts' => true,
	'delete_posts' => false,
	'delete_others_posts' => false,
));

//roles are created, so let's shut the front end for all non-logged in visitors and block the WP ADMIN & WP ADMIN BAR from
// everyone below our ourselves and our staff by blocking users.  This can be changed in the future to allow for 
// 'client-contacts' [created above] to have access, or even install WP CR[a]M on a site that has an operational front-end
// site, which will good for the time we build the BILLING & INVOICING add-on and CLIENT SUPPORT MANAGMENT add-on.  
// out in the next major release along with additional client interactive features as well.  

//block the front end of the site from everyone that is not logged in
function are_ye_worthy() {
	global $pagenow;
 
	if ( ! is_user_logged_in() && $pagenow != 'wp-login.php' )
		wp_redirect( wp_login_url(), 302 );
}

add_action( 'wp', 'are_ye_worthy' );

//hide WP ADMIN AREA from everyone below editor role via 'edit_others_posts' capability courtesy of my Can't Touch This plugin.
function cant_touch_this() {
	if ( !current_user_can( 'edit_others_posts' ) ){
		wp_redirect( home_url() ); exit;
	}
}
add_action( 'admin_init', 'cant_touch_this' );


//hides the WORDPRESS ADMIN BAR for everyone below editor role via 'edit_others_posts' capability
function hammer_time() { 
	if( ! current_user_can('edit_others_posts') )
		add_filter('show_admin_bar', '__return_false');	
}
add_action( 'after_setup_theme', 'hammer_time' );



//
//creating connection options between cram clients, projects and new custom roles
//

function wpcram_connections() {
	// ensure p2p is active and functioning first.
	if ( !function_exists( 'p2p_register_connection_type' ) ) {
		return;
	}

	//giving ability to assign 'client-contacts' roles to 'clients'
	p2p_register_connection_type( array(
		'name' => 'clients_have_contacts',
		'from' => 'clients',
		'to' => 'user',
		'reciprocal' => true,
		'cardinality' => 'one-to-many',
		'to_query_vars' => array( 'role' => 'client-contact' ),
		'title' => array(
		'from' => __( 'Client Contacts', 'my-textdomain' ),
		'to' => __( 'Contacts', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Client Contacts', 'my-textdomain' ),
		  'search_items' => __( 'Search Clients', 'my-textdomain' ),
		  'not_found' => __( 'No Clients found.', 'my-textdomain' ),
		  'create' => __( 'Create Connections', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Contact Clients', 'my-textdomain' ),
		  'search_items' => __( 'Search Contacts', 'my-textdomain' ),
		  'not_found' => __( 'No Contacts found.', 'my-textdomain' ),
		  'create' => __( 'Connect A Contact', 'my-textdomain' ),
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'side'
			)
		) );


	//giving ability to assign 'support' roles to 'clients'
	p2p_register_connection_type( array(
		'name' => 'clients_have_support',
		'from' => 'clients',
		'to' => 'user',
		'reciprocal' => true,
		'cardinality' => 'one-to-many',
		'to_query_vars' => array( 'role' => 'support' ),
		'title' => array(
		'from' => __( 'Assigned Support Reps', 'my-textdomain' ),
		'to' => __( 'Support Reps', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Supported Clients', 'my-textdomain' ),
		  'search_items' => __( 'Search Clients Supporting', 'my-textdomain' ),
		  'not_found' => __( 'No Supported Clients found.', 'my-textdomain' ),
		  'create' => __( 'Connect A Client', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Assigned Support Reps', 'my-textdomain' ),
		  'search_items' => __( 'Search Support Reps', 'my-textdomain' ),
		  'not_found' => __( 'No Support Reps found.', 'my-textdomain' ),
		  'create' => __( 'Assign A Support Rep', 'my-textdomain' ),
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'side'
			)
		) );

	//giving ability to assign 'project-manager' roles to 'projects'
	p2p_register_connection_type( array(
		'name' => 'projects_have_project_managers',
		'from' => 'projects',
		'to' => 'user',
		'reciprocal' => true,
		'cardinality' => 'one-to-many',
		'to_query_vars' => array( 'role' => 'project-manager' ),
		'title' => array(
		'from' => __( 'Assigned Project Manager', 'my-textdomain' ),
		'to' => __( 'Project Manager', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Projects Managing', 'my-textdomain' ),
		  'search_items' => __( 'Search Projects Managing', 'my-textdomain' ),
		  'not_found' => __( 'No Managed Projects found.', 'my-textdomain' ),
		  'create' => __( 'Manage A Project', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Assigned Project Manager:', 'my-textdomain' ),
		  'search_items' => __( 'Search Project Managers', 'my-textdomain' ),
		  'not_found' => __( 'No Project Managers found.', 'my-textdomain' ),
		  'create' => __( 'Assign A Project Manager', 'my-textdomain' ),
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'side'
			)
		) );

	//giving ability to assign 'task-manager' roles to 'tasks'
	p2p_register_connection_type( array(
		'name' => 'tasks_have_task_managers',
		'from' => 'tasks',
		'to' => 'user',
		'reciprocal' => true,
		'cardinality' => 'one-to-many',
		'to_query_vars' => array( 'role' => 'task-manager' ),
		'title' => array(
		'from' => __( 'Assigned Task Manager:', 'my-textdomain' ),
		'to' => __( 'Assign A Task Manager', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Tasks Managing', 'my-textdomain' ),
		  'search_items' => __( 'Search Tasks Managing', 'my-textdomain' ),
		  'not_found' => __( 'No Managed Tasks found.', 'my-textdomain' ),
		  'create' => __( 'Manage A Task', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Assigned Task Manager:', 'my-textdomain' ),
		  'search_items' => __( 'Search Task Managers', 'my-textdomain' ),
		  'not_found' => __( 'No Task Managers found.', 'my-textdomain' ),
		  'create' => __( 'Assign A Task Manager', 'my-textdomain' ),
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'side'
			)
		) );

	//assign projects to a client
	p2p_register_connection_type( array(
		'name' => 'projects_for_client',
		'from' => 'projects',
		'to' => 'clients',
		'reciprocal' => true,
		'title' => array(
		'from' => __( 'Project For Client:', 'my-textdomain' ),
		'to' => __( 'Client Projects', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Project', 'my-textdomain' ),
		  'search_items' => __( 'Search Projects', 'my-textdomain' ),
		  'not_found' => __( 'No Projects found.', 'my-textdomain' ),
		  'create' => __( 'Connect A Project', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Clients', 'my-textdomain' ),
		  'search_items' => __( 'Search Clients', 'my-textdomain' ),
		  'not_found' => __( 'No Clients Found.', 'my-textdomain' ),
		  'create' => __( 'Connect A Client', 'my-textdomain' ),			
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'advanced'
			)
		) );

	//connect tasks to a client
	p2p_register_connection_type( array(
		'name' => 'tasks_for_client',
		'from' => 'tasks',
		'to' => 'clients',
		'reciprocal' => true,
		'title' => array(
		'from' => __( 'Task For Client:', 'my-textdomain' ),
		'to' => __( 'Client Tasks', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Task', 'my-textdomain' ),
		  'search_items' => __( 'Search Tasks', 'my-textdomain' ),
		  'not_found' => __( 'No Tasks found.', 'my-textdomain' ),
		  'create' => __( 'Connect Client To A Task', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Clients', 'my-textdomain' ),
		  'search_items' => __( 'Search Clients', 'my-textdomain' ),
		  'not_found' => __( 'No Clients Found.', 'my-textdomain' ),
		  'create' => __( 'Connect Task To A Client', 'my-textdomain' ),			
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'advanced'
			)
		) );

	//connect tasks to a project
	p2p_register_connection_type( array(
		'name' => 'tasks_for_project',
		'from' => 'tasks',
		'to' => 'projects',
		'reciprocal' => true,
		'title' => array(
		'from' => __( 'Task For Project:', 'my-textdomain' ),
		'to' => __( 'Project Tasks:', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Task', 'my-textdomain' ),
		  'search_items' => __( 'Search Tasks', 'my-textdomain' ),
		  'not_found' => __( 'No Tasks found.', 'my-textdomain' ),
		  'create' => __( 'Connect Project To A Task', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Projects', 'my-textdomain' ),
		  'search_items' => __( 'Search Projects', 'my-textdomain' ),
		  'not_found' => __( 'No Projects Found.', 'my-textdomain' ),
		  'create' => __( 'Connect Task To A Project', 'my-textdomain' ),			
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'advanced'
			)
		) );

	//connect clients to clients for referral / referree relationship
	p2p_register_connection_type( array(
		'name' => 'client_referrals_and_referrees',
		'from' => 'clients',
		'to' => 'clients',
		'reciprocal' => false,
		'title' => array(
		'from' => __( 'Client Has Referred:', 'my-textdomain' ),
		'to' => __( 'Referred By:', 'my-textdomain' )
		),
		'from_labels' => array(
		  'singular_name' => __( 'Referrees', 'my-textdomain' ),
		  'search_items' => __( 'Search Referrees', 'my-textdomain' ),
		  'not_found' => __( 'No Referrees Found.', 'my-textdomain' ),
		  'create' => __( 'Connect Referree To This Client', 'my-textdomain' ),
		),
		'to_labels' => array(
		  'singular_name' => __( 'Referrals', 'my-textdomain' ),
		  'search_items' => __( 'Search Referrals', 'my-textdomain' ),
		  'not_found' => __( 'No Referrals found.', 'my-textdomain' ),
		  'create' => __( 'Connect Referral To This Client', 'my-textdomain' ),			
		),		
		'admin_box' => array(
			'show' => 'any',
			'context' => 'side'
			)
		) );	


}
add_action( 'p2p_init', 'wpcram_connections', 50 );

//THIS IS THE
//TESTING GROUND
//EVERYTHING BELOW 
//THIS MUST BE 
//INTEGRATED PROPERLY 
//OR DELETED


?>