<?php require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php'); ?>
<?php 
// Execute any actions that have been coded into the theme/other plug-ins to run before the footer is output.
do_action('external_header_footer_pre_footer');

// Output the footer of the website.
get_footer(); 
?>