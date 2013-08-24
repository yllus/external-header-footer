<?php require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); ?>
<?php 
// If we've disallowed output, exit immediately.
if ( ( (int) get_option('ehf_expose_header_and_footer', 0) ) == 0 )  {
	exit;
}

// Execute any actions that have been coded into the theme/other plug-ins to run before the footer is output.
do_action('external_header_footer_pre_footer');

// Output the footer of the website.
get_footer(); 
?>