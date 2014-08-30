<?php
// Load in order to access standard WordPress functions.
require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Set the URLs for the header and footer files.
$str_header_url = plugins_url() . '/external-header-footer/header.php';
$str_footer_url = plugins_url() . '/external-header-footer/footer.php';

// Output the header to the page.
$arr_header = wp_remote_get($str_header_url);
echo $arr_header['body'];
?>

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
// Output the footer to the page.
$arr_footer = wp_remote_get($str_footer_url);
echo $arr_footer['body'];
?>