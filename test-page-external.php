<?php
// Load in order to access standard WordPress functions.
require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Output the external header to the page.
ehf_output_external_header();
?>

<p>
Right here is where content is displayed, wrapped by the header and footer of the external website.
</p>

<?php 
// Output the external footer to the page.
ehf_output_external_footer();
?>