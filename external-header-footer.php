<?php
/*
 Plugin Name: External Header Footer
 Plugin URI: https://github.com/yllus/external-header-footer
 Description: Exposes the header and footer of the website as individual files, allowing for external consumption (for third parties sites that want a similar design style).
 Author: Sully Syed
 Version: 1.0
 Author URI: http://yllus.com/
*/

// Add a new rewrite rule that points to our exposed header and footer.
function ehf_add_rewrite_rules_parameters() {
	add_rewrite_tag('%ehf_template%','([^&]+)');

	add_rewrite_rule( '^external-header-footer/([header|footer|demo]+)/?$', 'index.php?ehf_template=$matches[1]', 'top');
}
add_action('init', 'ehf_add_rewrite_rules_parameters', 1);

// Handle requests that contain the "ehf_template" parameter.
function ehf_parse_request( &$wp ) {
	if ( array_key_exists( 'ehf_template', $wp->query_vars ) ) {
        global $wp_query;

        // If we've disallowed output, exit immediately.
		if ( ( (int) get_option('ehf_expose_header_and_footer', 0) ) == 0 )  {
			return;
		}

        switch ( $wp->query_vars['ehf_template'] ) {
        	case 'header':
        		// Execute any actions that have been coded into the theme/other plug-ins to run before the footer is output.
				do_action('external_header_footer_pre_header');

				// Output the header of the website.
				get_header();
				exit;
        		break;
        	case 'footer':
    			// Execute any actions that have been coded into the theme/other plug-ins to run before the footer is output.
				do_action('external_header_footer_pre_footer');

				// Output the footer of the website.
				get_footer(); 
				exit;
				break;
			case 'demo':
				// Output the external header to the page.
				ehf_output_external_header();
				?>

				<p>
				Right here is where content is displayed; you should see the header and footer of the external website above and below this text.
				</p>

				<?php 
				// Output the external footer to the page.
				ehf_output_external_footer();
				exit;
				break;
        }
    }
}
add_action('parse_request', 'ehf_parse_request');

/**
 * Output the contents of the external header wherever the function ehf_output_external_header() is called.
 *
 * @return void
 */
function ehf_output_external_header() {
	if ( false === ( $str_external_header = get_transient('ehf_external_header_url') ) ) {
		// Get the URL to try to get to retrieve the header from. If it's blank, exit immediately.
		$ehf_external_header_url = get_option('ehf_external_header_url', '');
		if ( strlen($ehf_external_header_url) == 0 ) {
			return;
		}

		// Retrieve the external header.
		$arr_header = wp_remote_get($ehf_external_header_url);
		$str_external_header = $arr_header['body'];

		// Save the contents of the retrieved external header to the local cache (via the Transients API).
		$ehf_external_cache_expiry = get_option('ehf_external_cache_expiry', '60');
		set_transient('ehf_external_header_url', $str_external_header, $ehf_external_cache_expiry);
	}

	echo $str_external_header;
}

/**
 * Output the contents of the external footer wherever the function ehf_output_external_footer() is called.
 *
 * @return void
 */
function ehf_output_external_footer() {
	if ( false === ( $str_external_footer = get_transient('ehf_external_footer_url') ) ) {
		// Get the URL to try to get to retrieve the footer from. If it's blank, exit immediately.
		$ehf_external_footer_url = get_option('ehf_external_footer_url', '');
		if ( strlen($ehf_external_footer_url) == 0 ) {
			return;
		}

		// Retrieve the external footer.
		$arr_header = wp_remote_get($ehf_external_footer_url);
		$str_external_footer = $arr_header['body'];

		// Save the contents of the retrieved external footer to the local cache (via the Transients API).
		$ehf_external_cache_expiry = get_option('ehf_external_cache_expiry', '60');
		set_transient('ehf_external_footer_url', $str_external_footer, $ehf_external_cache_expiry);
	}

	echo $str_external_footer;
}

/**
 * Adds "External Header Footer" under the Settings menu, points the entry to be run by external_header_footer_do_settings_page().
 *
 * @return void
 */
function external_header_footer_settings_page() {
	add_options_page( 'External Header Footer Settings', 'External Header Footer', 'manage_options', 'external_header_footer_settings', 'external_header_footer_do_settings_page' );
}
add_action( 'admin_menu', 'external_header_footer_settings_page' );

/**
 * Outputs the overall External Header Footer settings page (the output of its fields get their own function, this contains the nonce and other stuff).
 *
 * @return void
 */
function external_header_footer_do_settings_page() {
	if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
	?>
    <div class="wrap">
    	<div id="icon-options-general" class="icon32"><br /></div>
    	<h2>External Header Footer Settings</h2>

		<form method="post" action="options.php">
			<table class="form-table">
				<tbody>
					<?php do_settings_sections('external_header_footer_settings_page'); ?>
				</tbody>
			</table>

			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>"/>
			</p>

			<?php settings_fields('external_header_footer_settings_group'); ?>	
		</form>
    </div>
    <?php
}

/**
 * Outputs the overall External Header Footer settings page (the output of its fields get their own function, this contains the nonce and other stuff).
 *
 * @return void
 */
function external_header_footer_init() {
    // Add the settings section that all of our fields will belong to (heading not shown).
    add_settings_section('external_header_footer_settings_section', '', 'ehf_header_footer_settings_section_text', 'external_header_footer_settings_page');

    // Add the "Expose Header and Footer" field (blank title here, output in its function), registered to the group "external_header_footer_settings_group", and 
    // output in the function ehf_expose_header_and_footer_checkbox().
    add_settings_field('ehf_expose_header_and_footer', '', 'ehf_expose_header_and_footer_checkbox', 'external_header_footer_settings_page', 'external_header_footer_settings_section');
    register_setting('external_header_footer_settings_group', 'ehf_expose_header_and_footer', 'ehf_flush_rewrite_rules');

    // Add the "External Header URL" field (blank title here, output in its function), registered to the group "external_header_footer_settings_group", output in 
    // the function ext_external_header_url_text() and using the sanitizing function of ehf_external_clear_cache().
	add_settings_field('ehf_external_header_url', '', 'ext_external_header_url_text', 'external_header_footer_settings_page', 'external_header_footer_settings_section');
    register_setting('external_header_footer_settings_group', 'ehf_external_header_url', 'ehf_external_clear_cache');

    // Add the "External Footer URL" field (blank title here, output in its function), registered to the group "external_header_footer_settings_group", output in 
    // the function ext_external_footer_url_text() and using the sanitizing function of ehf_external_clear_cache().
	add_settings_field('ehf_external_footer_url', '', 'ext_external_footer_url_text', 'external_header_footer_settings_page', 'external_header_footer_settings_section');
    register_setting('external_header_footer_settings_group', 'ehf_external_footer_url', 'ehf_external_clear_cache');

    // Add the "Cache External Header/Footer Expiry" field (blank title here, output in its function), registered to the group "external_header_footer_settings_group", and
    // output in the function ext_external_footer_url_text().
	add_settings_field('ehf_external_cache_expiry', '', 'ehf_external_cache_expiry_text', 'external_header_footer_settings_page', 'external_header_footer_settings_section');
    register_setting('external_header_footer_settings_group', 'ehf_external_cache_expiry');
}
add_action('admin_init', 'external_header_footer_init');

function ehf_header_footer_settings_section_text() {
	1;
}

function ehf_expose_header_and_footer_checkbox() {
	// Retrieve and expose the "Expose Header and Footer" setting.
	$ehf_expose_header_and_footer = (int) get_option('ehf_expose_header_and_footer', 0);
	$ehf_expose_header_and_footer_checked = '';
	if ( $ehf_expose_header_and_footer == 1 ) {
		$ehf_expose_header_and_footer_checked = ' checked="checked"';
	}

	// Retrieve the URLs for the header, footer and test page.
	$ehf_header_url = home_url('/external-header-footer/header/');
	$ehf_footer_url = home_url('/external-header-footer/footer/');
	$ehf_test_url = plugins_url('external-header-footer/test-page.php');
	?>
	<tr valign="top">
		<th colspan="2">
			<h3>Expose Header for External Sites</h3>
			<p style="font-weight: normal;">
				If you're got an external website that you'd like to dress up with the same header and footer as this WordPress site, check the <b>Expose Header and Footer</b> 
				option below, and run a script on that external site to pull down the contents of the <b>Header URL</b> and <b>Footer URL</b> on a regular basis to keep the 
				two site looking the same.
			</p>
			<p style="font-weight: normal;">
				Once you've checked <b>Expose Header and Footer</b> and pressed the <b>Save Changes</b> button to enable the option, check out the page at <b>Demo Page URL</b>, 
				and take a look at its source code to see an example of how to retrieve and display the header and footer of this website using PHP.
			</p>
		</th>
	</tr>

	<tr valign="top">
		<th scope="row">Expose Header and Footer</th>
		<td> 
			<legend class="screen-reader-text"><span>Expose Header and Footer</span></legend>
			<label for="ehf_expose_header_and_footer">
				<input name="ehf_expose_header_and_footer" type="checkbox" id="ehf_expose_header_and_footer" value="1" <?php echo $ehf_expose_header_and_footer_checked; ?>/> 
				Allow this site's header and footer can be consumed by other websites
			</label>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="ext_header_url">Header URL</label>
		</th>
		<td>
			<code><a target="_blank" href="<?php echo $ehf_header_url; ?>"><?php echo $ehf_header_url; ?></a></code>
			<p class="description">Provide this URL to those looking to display this site's header on another website. (Remember, you can modify the output of the URL above through use of the <code>external_header_footer_pre_header()</code> action.)
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="ext_footer_url">Footer URL</label>
		</th>
		<td>
			<code><a target="_blank" href="<?php echo $ehf_footer_url; ?>"><?php echo $ehf_footer_url; ?></a></code>
			<p class="description">Provide this URL to those looking to display this site's footer on another website. (Remember, you can modify the output of the URL above through use of the <code>external_header_footer_pre_footer()</code> action.)
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="ext_footer_url">Demo Page URL</label>
		</th>
		<td>
			<code><a target="_blank" href="<?php echo $ehf_test_url; ?>"><?php echo $ehf_test_url; ?></a></code>
			<p class="description">This page acts as a demonstration of what a page on this website would look like wrapped with an external site's header and footer.</p>
		</td>
	</tr>
	<?php
}

function ehf_consume_header_and_footer_checkbox() {
	// Retrieve and expose the "Consume Header and Footer" setting.
	$ehf_consume_header_and_footer = (int) get_option('ehf_consume_header_and_footer', 0);
	$ehf_consume_header_and_footer_checked = '';
	if ( $ehf_consume_header_and_footer == 1 ) {
		$ehf_consume_header_and_footer_checked = ' checked="checked"';
	}
	?>
	<tr valign="top">
		<th scope="row">Consume External Header / Footer</th>
		<td> 
			<legend class="screen-reader-text"><span>Consume Header and Footer</span></legend>
			<label for="ehf_consume_header_and_footer">
				<input name="ehf_consume_header_and_footer" type="checkbox" id="ehf_consume_header_and_footer" value="1" <?php echo $ehf_consume_header_and_footer_checked; ?>/> 
				If checked, the <code>ehf_output_external_header()</code> and <code>ehf_output_external_footer()</code> functions will output the contents of the header and footer URLs listed below
			</label>
		</td>
	</tr>
	<?php
}

function ext_external_header_url_text() {
	// Retrieve the URL for the external header.
	$ehf_external_header_url = get_option('ehf_external_header_url', '');
	?>
	<tr valign="top">
		<th colspan="2">
			<h3>Consume Header / Footer from External Website</h3>
			<p style="font-weight: normal;">
				If you've enabled the External Header Footer plug-in on another WordPress website, and want to use its header on <i>this</i> WordPress website, you can use the 
				fields below to automatically retrieve the header and footer of that website. 
			</p>
			<p style="font-weight: normal;">
				Next, update the <code>header.php</code> and <code>footer.php</code> files of this WordPress theme to call the function <code>ehf_output_external_header()</code> 
				and <code>ehf_output_external_footer()</code> respectively. This plug-in will automatically retrieve and cache the contents of the external site's header and 
				footer for the amount of minutes specified in <b>Cache Header/Footer For</b>.
		</th>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="ehf_external_header_url">External Header URL</label></th>
		<td>
			<input name="ehf_external_header_url" type="text" id="ehf_external_header_url" value="<?php echo $ehf_external_header_url; ?>" class="regular-text code" style="width: 600px;" />
			<p class="description">The function <code>ehf_output_external_header()</code> will output the contents of the page retrieved at the URL input into the field above.</p>
		</td>
	</tr>
	<?php
}

function ext_external_footer_url_text() {
	// Retrieve the URL for the external footer.
	$ehf_external_footer_url = get_option('ehf_external_footer_url', '');
	?>
	<tr valign="top">
		<th scope="row"><label for="ehf_external_footer_url">External Footer URL</label></th>
		<td>
			<input name="ehf_external_footer_url" type="text" id="ehf_external_footer_url" value="<?php echo $ehf_external_footer_url; ?>" class="regular-text code" style="width: 600px;" />
			<p class="description">The function <code>ehf_output_external_footer()</code> will output the contents of the page retrieved at the URL input into the field above.</p>
		</td>
	</tr>		
	<?php
}

function ehf_external_cache_expiry_text() {
	// Retrieve the cache expiry time limit (in minutes).
	$ehf_external_cache_expiry = get_option('ehf_external_cache_expiry', '60');

	// Retrieve the URLs for the external test page.
	$ehf_external_test_url = home_url('/external-header-footer/demo/');
	?>
	<tr valign="top">
		<th scope="row"><label for="ehf_external_cache_expiry">Cache Header/Footer For</label></th>
		<td>
			<input name="ehf_external_cache_expiry" type="text" id="ehf_external_cache_expiry" value="<?php echo $ehf_external_cache_expiry; ?>" class="regular-text" style="width: 75px;" /> minutes
			<p class="description">The amount of time that the external header/footer should be cached locally for before being retrieved again.</p>
		</td>
	</tr>	

	<tr valign="top">
		<th scope="row">
			<label for="ext_footer_url">External Demo Page URL</label>
		</th>
		<td>
			<code><a target="_blank" href="<?php echo $ehf_external_test_url; ?>"><?php echo $ehf_external_test_url; ?></a></code>
			<p class="description">This page demonstrates what an external page wrapped with the specified external header and footer would look like.</p>
		</td>
	</tr>	
	<?php	
}

/**
 * Called when a new value is sent to the "Expose Header and Footer" field; flushes the internal cache of WordPress rewrite rules / permalinks 
 * to ensure the new rules for the plug-in are accessible.
 *
 * @return string
 */
function ehf_external_clear_cache( $value ) {
	delete_transient('ehf_external_header_url');
	delete_transient('ehf_external_footer_url');

	return $value;
}

/**
 * Called when a new value is sent to the "External Header URL" or "External Footer URL"; clears the Transients API cache of 
 * what may already be saved to those fields to ensure changes to what is wished to be retrieved occurs immediately.
 *
 * @return integer
 */
function ehf_flush_rewrite_rules( $value ) {
	global $wp_rewrite;

	$wp_rewrite->flush_rules( false );

	return $value;
}
?>