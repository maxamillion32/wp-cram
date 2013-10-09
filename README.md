# WP CR[a]M
Converts A Fresh WordPress installation into a CRM.  Originally this was built to help me manage my clients, projects and tasks however I felt that since the current WP CRM options either don't do enough or do too much, I thought I'd share this so that everyone has a nice base to build their own CRM off of out of WordPress.

***As mentioned above, this was originally built for myself, therefore it's somewhat geared towards Web Development and/or Web Design, however, you can easily fork this and modify it to work for any business at all with a bit of code.***

***Screenshots Coming Soon***

## Notice & Credits
###IMPORTANT NOTICE:
It's important to note that *this plugin locks down wordpress for non-logged in users* as it stands and *should not be used on a public or production site*.  This plugin does not style any non-admin pages/posts relative to projects, tasks, clients, etc.  I would love to find the time to put together the code to create pre-built templates that injected the taxos and custom post types into the theme of the user's choice so that we can integrate customer communication and billing via WP CR[a]M - and I have plans to in the future - so if you would like to do so, feel free to *fork and pull* to your heart's desire!  And please do feel free to notify me of your mods in that direction (I will be adding my current WP CR[a]M task list in the future, time permitting).

###CREDITS:
WP-CR[a]M uses two very sexy plugins in it's core.  Respect & Credits go to the authors of these plugins.  Here is a list of the interior plugins that I used to save me countless hours in creating WP CR[a]M to make it function properly:
<ul>
	<li>MP6: Used for it's sexy WP Admin UI, until I have time to create my own</li>
	<li>Post 2 Post: Handles relationships between projects/tasks/clients/contacts</li>
</ul>

In order to load these interior plugins correctly, their plugin headers have been
removed.  If you have any questions about these plugins and how they function 
independently from WP-CR[a]M, please use the links above to contact the plugin
developers.

#Current Features
1. Clients (CPT with custom taxonomies) *Not to be confused with **users**, clients can be companies or people*

<ul>
	<li>Clients come standard with some basic metaboxes for basic data fields such as contact information, social media profiles and client credentials.</li>
	<li>You can associate Clients to Client Statuses(taxo) There are 8 preset Client Statuses and you can easily create your own in the wp-admin area under `clients > client status > create new`
		<ul>
			<li>Active Client *Currently working with this client on REGULAR BASIS.*</li>
			<li>Average Client *This is an AVERAGE CLIENT that has projects/tasks at an average rate.*</li>
			<li>Freebie Client *self explanatory*</li>
			<li>Legacy Client *LONGSTANDING CLIENT that you have worked with for a long time.*</li>
			<li>Past Client *This is a client that you NO LONGER DO BUSINESS WITH.*</li>
			<li>Prospective Client *This is a POTENTIAL CLIENT and no projects or tasks have been started.*</li>
			<li>Referral Client *This is a client that REFERS OTHER CLIENTS TO YOU.*</li>
			<li>Referred Client *This is a client that WAS REFERRED TO YOU BY ANOTHER CLIENT.*</li>
		</ul>
	</li>
	<li>You can associate Clients to Client Contacts, Assigned Support Reps, Referred Clients and Referral Clients *these user roles are preset custom user roles with custom capabilities based on the role*</li>
	<li>You can assign a Client to a Client Type(taxo)
		<ul>
			<li>Client Types are *not preset* so that you can use terminologies that you prefer like "design", "development", "seo" or whatever your heart desires.</li>
		</ul>
	</li>
	<li>You can associate Clients to Projects</li>
	<li>You can associate Clients to Tasks</li>
</ul>
2. Projects (CPT with custom taxonomies) *Quick Note: Projects are big, Tasks are small, Projects can be compiled with multiple Tasks.*

<ul>
	<li>Projects come standard with some basic metaboxes for collecing the standard project data such as start date, deadline date, project scope, project credentials, total hours and hours billed</li>
	<li>Projects come standard with basic preset Project Statuses (taxo)
		<ul>
			<li>Active Project *This is an active project, currently IN PROGRESS.*</li>
			<li>Client Reviewing Project *This project is currently being REVIEWED BY CLIENT.*</li>
			<li>Internal Project Review *This project is currently being REVIEWED INTERNALLY.*</li>
			<li>Pending Project *This project is currently INACTIVE AND PENDING.*</li>
			<li>Project Completed *This project has been reviewed and COMPLETED. NO WORK NEEDED.*</li>
		</ul>
	</li>
	<li>Projects can be assigned Project Managers and Tasks</li>
	<li>You can assign a Project to a Project Type (taxo)
		<ul>
			<li>Project Types are *not preset* following the same logic as Client Types so that you may use them as you wish.</li>
		</ul>
	</li>
</ul>

3. Tasks (CPT with custom taxonomies) *Quick Note: Tasks are small, Projects are big, Tasks can be assigned to a specific project*

<ul>
	<li>Tasks come standard with some basic metaxoes for collecting the standard task data such as start date, deadline, task scope, necessary credentials, total hours and hours billed.</li>
	<li>Tasks come standard with basic preset Task Statuses (taxo)
		<ul>
			<li>Active Task *This is an active task, currently IN PROGRESS.*</li>
			<li>Client Reviewing Task *This task is currently being REVIEWED BY CLIENT.*</li>
			<li>Internal Task Review *This task is currently being REVIEWED INTERNALLY.*</li>
			<li>Pending Task *This task is currently INACTIVE AND PENDING.*</li>
			<li>Task Completed *This task has been reviewed and COMPLETED. NO WORK NEEDED.*</li>
		</ul>
	</li>
	<li>Tasks can be assigned Task Managers</li>
	<li>Tasks can be associated with a specific client</li>
	<li>Tasks can be associated with a specific project.</li>
	<li>You can assign a Task to a Task Type (taxo)
		<ul>
			<li>Task Types are *not preset* following the same logic as Client Types & Project Types so that you may use them as you wish.</li>
		</ul>
	</li>
</ul>

4. Custom User Roles *Custom user roles were created to help organize future users of the plugin from a management standpoint*

<ul>
	<li>Client Contact *Client contacts are your contact(s) for the "Client" - which would typically be a company that you are doing work for*. ***Capabilities listed below:***
		<ul>
			<li>`read`</li>
		</ul>
	</li>
	<li>Support *Support role is for employees that you would want to have access to your Clients, Projects and Tasks to help you faciliate and manage your clients, projects and tasks.* ***Capabilities listed below:***
		<ul>
			<li>`read`</li>
			<li>`edit_posts`</li>
			<li>`edit_others_posts`</li>
			<li>`delete_posts` <= set to false to explicitly prevent this in the event that 'delete_posts' ever gets included into 'edit_posts' in wp core in the future</li>
		</ul>
	</li>
	<li>Task Manager *This is so you can assign an employee to handle this specific task* ***Capabilities listed below:***
		<ul>
			<li>`read`</li>
			<li>`edit_posts`</li>
			<li>`edit_others_posts`</li>
			<li>`delete_posts` <= set to false to explicitly prevent this in the event that 'delete_posts' ever gets included into 'edit_posts' in wp core in the future</li>
		</ul>
	</li>
	<li>Project Manager *Project Managers are assigned individuals within your organization that have been tasked with managing a specific project* ***Capabilities listed below:***
		<ul>
			<li>`read`</li>
			<li>`edit_posts`</li>
			<li>`edit_others_posts`</li>
			<li>`delete_posts` <= set to false to explicitly prevent this in the event that 'delete_posts' ever gets included into 'edit_posts' in wp core in the future</li>
		</ul>
	</li>
</ul>

#Future Features

***Please note that I have no active schedule to implement any of these features as they all depend on available time and resources, though I do have full intentions on adding them in the future***

<ul>
	<li>Dashboard Reports & Analytics to better manage pending tasks, projects, appointements, etc.</li>
	<li>Front End usage for communicating with clients through WP CR[a]M and keeping them updated on the status of their projects/tasks as you see fit.  This will also allow you to optionally use the comments system as a communication tool within the CRM - which has been activated during the custom post type registration for all cpts in this plugin.</li>
	<li>Twilio API Integration so that calls and text messages to clients can be made from your browser or cellphone directly from a 'Client' screen.</li>
</ul>


### Usage

#COMING SOON