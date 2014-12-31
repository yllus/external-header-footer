<?php
/* This file consists of a demonstration script for how to retrieve the header and footer of a WordPress site 
 * that has the External Header Footer plug-in enabled. It's meant to be completely standalone of WordPress 
 * itself, and uses pure PHP functions only.
 *
 * If you want to take the header/footer of one WordPress site and use it on a second WordPress site, look 
 * at the "Consume Header / Footer from External Website" section of the plug-in's Settings page instead.
 */

// Take a guess at what the URLs to the WordPress site's exposed header and footer are...
$str_header_url = $_SERVER['SERVER_NAME'] . '/external-header-footer/header/';
$str_footer_url = $_SERVER['SERVER_NAME'] . '/external-header-footer/footer/';

// Define a function that uses the cURL library to retrieve the contents of a URL and returns it in a string.
function curl_download( $url ) {
    if ( !function_exists('curl_init') ) {
        die('cURL is not installed. Install and try again.');
    }
  
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
  
    return $output;
}
?>

<?php
// Retrieve and output the header to the page.
echo curl_download($str_header_url);
?>

<!-- Output some generic HTML text; this is the middle area between the consumed header and footer where you'd display site content. -->
<p>
Right here is where content is displayed, wrapped by the header and footer of this website.
<br /><br />
The URL to the site header is: <a target="_blank" href="<?php echo $str_header_url; ?>"><?php echo $str_header_url; ?></a>
<br />
The URL to the site footer is: <a target="_blank" href="<?php echo $str_footer_url; ?>"><?php echo $str_footer_url; ?></a>
<br /><br />
Thanks for using the <b>External Header Footer</b> plug-in! Please write to <a href="mailto:sully@yllus.com">sully@yllus.com</a> if you encounter issues or have suggestions.
</p>

<?php 
// Retrieve and output the footer to the page.
echo curl_download($str_footer_url);
?>