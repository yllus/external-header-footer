# External Header Footer #

**External Header Footer** is a simple WordPress plug-in that exposes that site's header and footer over HTTP as URLs whose content can then be consumed. 

How is this useful? Let's say you've got a WordPress website at your **www** subdomain, and a forum for your community of users at a **forums** subdomain. 
In many cases, you'll want to keep the header, footer and basic styling (link, text, background colours) of both sites identical. 

That's the cue to use **External Header Footer**: Once this plug-in is enabled on **www**, run a scheduled task on your **forums** subdomain to retrieve 
and locally store (write to a file, or to memory) the header and footer to be displayed on that site every day/hour/week. Now with minimal effort you can 
be assured that both sites will maintain a consistent look and feel.


## Setup & Use ##

### Making Your Website's Header and Footer Available for Consumption ##

1. Enable **External Header Footer** within the **Plugins** > **Installed Plugins** interface.

2. Head over next to the settings page for the plug-in at **Settings** > **External Header Footer**.

3. Check the **Expose Header and Footer** checkbox and click the **Save Changes** button.

4. The settings page should return with a message indicating your changes have been saved. The page will also list three URLs that are now ready for 
   viewing and use. The test page provides a demonstration of what some sample content will look like when the header and footer are read in and then 
   displayed at the top and bottom of that page - check out the (very straightforward) source code of test-page.php to understand how to use this 
   plug-in on your external websites.

5. On the WordPress side of things, your work is done. To finish up, set up your external website(s) to retrieve the header and footer at the URLs listed 
   above, sandwiching the actual content of the site in between. 

### Consuming an External Header and Footer to display on your WordPress Site ###

1. If not already done, enable **External Header Footer** within the **Plugins** > **Installed Plugins** interface.

2. Head over next to the settings page for the plug-in at **Settings** > **External Header Footer**.

3. Enter a valid URL into the **External Header URL** and **External Footer URL** fields (or leave one or the other blank if you only need one of the two).

4. Verify that the **Cache Header/Footer For** setting contains an appopriate value that is denominated in minutes.

5. Click the **Save Changes** button.

6. The settings page should return with a message indicating your changes have been saved. Next, click on the URL for the **External Demo Page URL** to 
   see a demonstration of what a page wrapped with the specified external header and footer would appear like.

7. To display the external header on a page, simply call the following function:

        ehf_output_external_header();

   Similarly, call the following function to output the external footer to a page:

        ehf_output_external_footer();

   Note: Through use of the best caching system available on your WordPress site, the HTML markup for the header and footer will be saved to your local 
   WordPress website, and only re-retreived when the cache expiry value is reached. Of course, any CSS, JavaScript or image assets on the external site 
   will be retrieved from that site by your site visitors.


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

* Need to clear the cache of an external header/footer immediately? No problem - clicking the **Save Changes** button on the settings page for this plug-in 
  do exactly that.
