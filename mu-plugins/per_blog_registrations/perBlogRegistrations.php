<?php
/*
Plugin Name: WPMU Per-Blog Registrations
Plugin URI: http://cctools.svn.sourceforge.net/viewvc/cctools/ccwordpress/trunk/mu-plugins/per_blog_registrations/
Description: Allows user of Wordpress MU to register with sub-blogs, not just the default blog.  It also allows you to disallow users to create new blogs, only allowing them to register with an existing blog.
Author: Nathan Kinkade
Author URI: http://creativecommons.org/
*/

/**
 * (c) 2007, Nathan Kinkade, Creative Commons
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
 * NOTE: The goal of this plugin is to achieve the desired result
 * without having to alter a single character of the base Wordpress MU
 * install.  This will ensure maximum portability and ease of upgrade
 * until the contributors at WPMU make changes that correct the issues
 * this plugin addresses.  However, what this means is that some of
 * these fixes are a bit hack-like.  This plugin may break something 
 * else or have some other undesirable side effect, though it seems to 
 * work well enough.  If you find a problem with this plugin or have 
 * suggestions or additions then contact the author at:
 * nkinkadeATcreativecommonsDOTorg.
 *
 * NOTE: This plugin does NOT provided "true" per-blog registrations.
 * That is, it will appear to the user that they are registering with a
 * particular blog, but in truth they will be able to login to any blog,
 * however a blog admin can assign a role for that user - just as they
 * would be able to under a normal MU install.
 */

/**
 * Force $active_signup to "user" no matter what.  wp-signup.php uses
 * this variable to determine what to display to the user during
 * sign-up, and also to set some others things.  As long as this is 
 * "user" then the user probably won't be able to register anything
 * but a regular subscriber account
 */
function pbr_disallowNewBlogs($active_signup) {

	# Use this to disable new blog creation and only allow user
	# registrations
	return "user";

	# Use this if you want MU to behave in the default fashion
	# return $active_signup;

}

/**
 * Here we force the value of $current_site->domain to the domain of the
 * blog under which the user is registering.  This should keep all the
 * displayed domains and links consistent, avoiding that some other
 * domain, namely wpmu's default domain, should show up and confuse or
 * potentially redirect the user to some other server.
 */
function pbr_setCurrentSiteDomain() {

	global $current_site, $current_blog;

	$current_site->domain = $current_blog->domain;

	# While we are at it, go ahead and change the site_name to the
	# domain.  This is optional.
	$current_site->site_name = $current_site->domain;

	return true;

}

/**
 * Set the users primary_blog and source_domain to the domain/blog
 * under which they launched the registration process.
 */
function pbr_setPrimaryBlog($userId) {

        global $current_blog, $blog_id;

        update_usermeta($userId, 'source_domain', $current_blog->domain); 
        update_usermeta($userId, 'primary_blog', $current_blog->blog_id); 

		# We also need to drop the current blog_id into the global namespace
		# because wpmu_activate_signup() calls function add_user_to_blog()
	 	# with a forced blog_id of "1" and then that function globalizes it 
		$blog_id = $current_blog->blog_id;

        return true;

}

/**
 * By default WPMU forces user-only (no blog) registrations to have
 * rights only in the default domain.  This users permissions be set to 
 * the blog under which the user signed up.
 */
function pbr_setUserCapabilities($userId, $userRole) {
	
	global $current_blog, $wpdb, $table_prefix;

	# Alters $wpdb->prefix to reflect the correct prefix for the primary domain
	switch_to_blog(1); 
	# We have to delete the key that WPMU set by default for blog "1"
	$wpdb->query("DELETE FROM $wpdb->usermeta WHERE user_id = '$userId' AND meta_key = '{$wpdb->prefix}capabilities'");

	# Now set the new capabilities
	# Alters $wpdb->prefix to reflect the correct prefix for this blog
	switch_to_blog($current_blog->blog_id); 
	$user = new WP_User($userId);
	$user->set_role($userRole);

}

/**
 * There is apparently something broken about Wordpress MU v1.2.3 in
 * regard to the "Register" link located under the "Meta" widget.  It
 * points to wp-login.php?action=register, whereas the actual
 * registration script is located at wp-signup.php.  This will check to
 * see if the registration link points to that bad URL and if so, fixes
 * it to point to the right place
 */
function pbr_fixSidebarRegisterLink($registerUrl) {

	$newUrl = preg_replace("/wp-login\.php\?action=register/", "wp-signup.php", $registerUrl);

	return $newUrl;

}

/**
 * This is a horrible hack, and it would probably be easier to just
 * modify the file wp-login.php manually, but that wouldn't be keeping
 * with the idea of not altering the MU code.  This function will check
 * for the presence of the variable $hasLostPassword and $isLoggingIn,
 * and if they are set will cause bloginfo('wpurl') to append
 * /wp-signup.php, BUT only if it is the first or second time,
 * respectively, that bloginfo('[wp]url') has been called, which
 * corresponds to the printing of the Register link, the other links
 * should not be modified.  The Register URL will look a little borked,
 * but it should work.
 */
function pbr_fixRegisterLink($blogUrl) {

	global $hasLostPassword, $isLoggingIn, $fixedLinkCount;

	if ( "true" == $isLoggingIn ) {
   		$fixedLinkCount++;
		# We only do this if users can actually register and a
		# "Register" link will be supplied to the user.
		if ( get_option('users_can_register') ) {
			if ( 1 == $fixedLinkCount ) {
				$blogUrl = "{$blogUrl}/wp-signup.php";
			}
		}
	}

	if ( "true" == $hasLostPassword ) {
   		$fixedLinkCount++;
		# We only do this if users can actually register and a
		# "Register" link will be supplied to the user.
		if ( get_option('users_can_register') ) {
			if ( 2 == $fixedLinkCount ) {
				$blogUrl = "{$blogUrl}/wp-signup.php";
			}
		}
	}

	return $blogUrl;

}

/**
 * This sets a global variable flagging the fact that we are at the
 * login page.  Used by filter pbr_fixRegisterLink()
 */
function pbr_setIsLoggingIn() {
	
	global $isLoggingIn;

	$isLoggingIn  = "true";

}

/**
 * This sets a global variable flagging the fact that we are at the lost
 * password page.  Used by filter pbr_fixRegisterLink()
 */
function pbr_setHasLostPassword() {
	
	global $hasLostPassword;

	$hasLostPassword  = "true";

}

/**
 * ACTIONS
 */
add_action("signup_header", "pbr_setCurrentSiteDomain");
add_action("activate_header", "pbr_setCurrentSiteDomain");
add_action("wpmu_activate_user", "pbr_setPrimaryBlog");
add_action("add_user_to_blog", "pbr_setUserCapabilities", 10, 2);
add_action("login_form", "pbr_setIsLoggingIn");
add_action("lostpassword_form", "pbr_setHasLostPassword");

/**
 * FILTERS
 */
add_filter("wpmu_active_signup", "pbr_disallowNewBlogs", 20);
add_filter("register", "pbr_fixSidebarRegisterLink");
add_filter("bloginfo_url", "pbr_fixRegisterLink");

?>
