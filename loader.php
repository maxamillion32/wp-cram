<?php
/*
Plugin Name: WP-CR[a]M
Description: Turn your WordPress Installation into a CRM to manage your clients and projects!
Version: 1.0
Author: Kevin Smothers
Author URI: http://VegasKev.com
License: GPL2...of course
*/

/**********************************************************************************/
/**********************************************************************************/
/*

NOTICE: WP-CR[a]M uses multiple plugins in it's core.  Respect & Credits go to 
the authors of these plugins.  Here is a list of the interior plugins used to make
WP-CR[a]M function properly:
1. MP6: Used for it's sexy WP Admin UI, until I have time to create my own
	http://wordpress.org/plugins/mp6/
2. Post 2 Post: Handles relationships between projects/tasks/clients/contacts
	http://wordpress.org/plugins/posts-to-posts/
3. CR[a]M Clients: Handles Clients for WP-CR[a]M
	Not in repository, I wrote this for WP-CR[a]M specifically
4. CR[a]M Projects: Handles Projects for WP-CR[a]M
	Not in repository, I wrote this for WP-CR[a]M specifically
5. CR[a]M Tasks: Handles Tasks for WP-CR[a]M
	Not in repository, I wrote this for WP-CR[a]M specifically	

In order to load these interior plugins correctly, their plugin headers have been
removed.  If you have any questions about these plugins and how they function 
independently from WP-CR[a]M, please use the links above to contact the plugin
developers.

*/
/**********************************************************************************/
/**********************************************************************************/


//Load interior plugins in proper order

include dirname(__FILE__) . '/wp-posts-to-posts/posts-to-posts.php';
include dirname(__FILE__) . '/mp6/mp6.php';
include dirname(__FILE__) . '/cram-clients/cram-clients.php';
include dirname(__FILE__) . '/cram-projects/cram-projects.php';
include dirname(__FILE__) . '/cram-tasks/cram-tasks.php';
include dirname(__FILE__) . '/cram-core.php';
include dirname(__FILE__) . '/cram-login/cram-login.php';

?>