<?php
/*
Plugin Name: WPMU Per-Blog Registrations
Plugin URI: http://code.creativecommons.org/viewsvn/wordpressmu/trunk/mu-plugins/per_blog_registrations/perBlogRegistrations.php?view=log
Description: Allows user of Wordpress MU to register with sub-blogs, not just the default blog.
Author: Nathan Kinkade (nkinkade@creativecommons.org)
Author URI: http://creativecommons.org/
*/

/**
 * (c) 2004-2007, Nathan Kinkade, Creative Commons
 *
 * Creative Commons has made the contents of this file available under a
 * CC-GNU-LGPL license:
 *
 * http://creativecommons.org/licenses/LGPL/2.1/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * pbr_ will be the prefix for functions to avoid name collisions.  No
 * relation to the similarly named beer, but stands for pER bLOG
 * rEGISTRATIONS.
 *
 * NOTE: This plugin does NOT provided "true" per-blog registrations in
 * a strict sense.  That is, it will appear to the user that they are
 * registering with a particular blog, but in truth they will be able to
 * login to any blog, however a blog admin can assign a role for that
 * user - just as they would be able to under a normal MU install.
 */

/**
 * Here we force the value of $current_site->{domain||path} to the
 * domain/path of the blog under which the user is registering.  This
 * should keep all the displayed links consistent, avoiding that some
 * other site, namely wpmu's default site, should show up and confuse or
 * potentially redirect the user to some other place.
 */
function pbr_setCurrentSite() {

	global $current_site, $current_blog;

	# Do different things depending on whether the site is using
	# subdomains or subdirectories
	if ( VHOST != "yes" ) {
		$current_site->path = $current_blog->path;
	} else {
		$current_site->domain = $current_blog->domain;
		# While we are at it, go ahead and change the site_name to the
		# domain.  This is optional.
		$current_site->site_name = $current_site->domain;
	}

	return true;

}

/**
 * Set the users primary_blog and source_domain to the domain/blog
 * under which they launched the registration process.
 */
function pbr_setPrimaryBlog($userId) {

	global $current_blog, $blog_id;

	update_usermeta($userId, 'primary_blog', $current_blog->blog_id); 

	if ( VHOST == "yes" ) {
		update_usermeta($userId, 'source_domain', $current_blog->domain); 
	}

	# we also need to drop the current blog_id into the global namespace
	# because wpmu_activate_signup() calls function add_user_to_blog()
 	# with a forced blog_id of "1" and then that function globalizes it 
	$blog_id = $current_blog->blog_id;

	return true;

}

/**
 * ACTIONS
 */
add_action("signup_header", "pbr_setCurrentSite");
add_action("activate_header", "pbr_setCurrentSite");
add_action("get_sidebar", "pbr_setCurrentSite");
add_action("wpmu_activate_user", "pbr_setPrimaryBlog");

?>
