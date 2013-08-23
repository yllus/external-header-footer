# External Header Footer #

**External Header Footer** is a simple WordPress plug-in that exposes that site's header and footer over HTTP as URLs whose content can then be consumed. 

How is this useful? Let's say you've got a WordPress website at your **www** subdomain, and a forum for your community of users at a **forums** subdomain. 
In many cases, you'll want to keep the header, footer and basic styling (link, text, background colours) of both sites identical. 

That's the cue to use **External Header Footer**: Once this plug-in is enabled on **www**, run a scheduled task on your **forums** subdomain to retrieve 
and locally store (write to a file, or to memory) the header and footer to be displayed on that site every day/hour/week. Now with minimal effort you can 
be assured that both sites will maintain a consistent look and feel.


## Setup & Use ##

1. Enable **External Header Footer** within the **Plugins** > **Installed Plugins** interface.

2. Immediately, three pages should now be available for viewing and use:
   * Header: http://yourdomain.com/wp-content/plugins/external-header-footer/header.php
   * Footer: http://yourdomain.com/wp-content/plugins/external-header-footer/header.php
   * Test Page: http://yourdomain.com/wp-content/plugins/external-header-footer/test-page.php
   
   The test page provides a demonstration of what some sample content will look like when the header and footer are read in and then displayed at the top 
   and bottom of that page - check out the (very straightforward) source code of test-page.php to understand how to use this plug-in on your external 
   websites.

3. On the WordPress side of things, your work is done. To finish up, set up your external website to retrieve the header and footer at the URLs listed 
   above, sandwiching the actual content of the site in between. 


## Advanced Usage ##

Sometimes the contents of your header or footer will cause conflicts or problems if displayed on an external website: Let's say you're using the popular 
**Google Analytics for WordPress** plug-in on your WordPress site, but your external website has its own Google Analytics code, and you don't want to get 
the two mixed together. No problem - but it will take a little bit of code. 

Two new WordPress Actions are included as part of this plug-in; both allow you to run your own code immediately before the external header and footer 
are displayed at their new individual URLs:

	external_header_footer_pre_header
	external_header_footer_pre_footer

Let's say you need to stop **Google Analytics for WordPress** from  In your theme's functions.php file, write a function that removes the addition of 
JavaScript code for Google Analytics by removing the action that adds to your site's header. Then simply call hook your function to the
**external_header_footer_pre_header** action. Here's an example of that code:

	function remove_ga_from_external_header() {
		// Remove the addition of AdSense JavaScript code from the header.
		remove_action( 'wp_head', array( 'GA_Filter', 'spool_adsense' ), 1 );

		// Remove the addition of Google Analytics JavaScript code from the header.
		remove_action( 'wp_head', array( 'GA_Filter', 'spool_analytics' ), 2 );
	}

	// Call the function remove_ga_from_external_header() immediately before the external header is displayed (at its individual URL).
	add_action('external_header_footer_pre_header', 'remove_ga_from_external_header');

Use the **external_header_footer_pre_footer** and the same technique - use of remove_action or remove_filter - to ensure only the code you want to go 
out as part of the footer is output.


## Tips & Tricks ##

* Avoid having the header and footer retrieved every time your external website displays a page. Instead, see if you can schedule a task to retrieve the 
  header and footer occasionally (say, once an hour) and save it to the external website's diskspace. Then output the contents of those files when pages 
  are requested on the external website. 

* Ensure that the URLs in your header and footer are absolute to ensure that they point to pages that exist when they're being displayed on an external 
  website. For example, if you've got the following URL coded into your header to point to your "About Us" page:

		<a href="/about-us/">About Us</a>

  You can use the standard WordPress function home_url() to ensure that the URL is output in absolute form:

  		<a href="<?php home_url('/about-us/'); ?>">About Us</a>

  For more information, check out the WordPress Codex entry for home_url at http://codex.wordpress.org/Function_Reference/home_url .


## To-Do ## 

* Write two short functions plus an admin page that allow for the specification of a header and footer URL. Those functions should then consume the 
  contents of those URLs, store them locally via the Transients API, and output them where the two functions are called. This will turn the plug-in 
  into a two-way street, useful for both provision and consumption of site header/footer styling.