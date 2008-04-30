<?php
/*
Plugin Name: Odiogo Listen Button
Plugin URI: http://www.odiogo.com/download/wordpress/plugin/odiogo_listen_button_latest.php
Description: Give your blog a voice! Add a "Listen Now" button to your blog so your readers can listen to your posts and download podcasts. <a href="http://www.odiogo.com/sign_up.php">Free Sign up</a>.
Author: Odiogo
Version: 2.5.2
Author URI: http://www.odiogo.com/
*/

/*
Version 1.7 by patricek (http://www.odiogo.com)
Version 2.1 by ozh (http://planetozh.com/blog)
Version 2.5 by patricek (http://www.odiogo.com)
*/

define ("ODIOGO_VERSION", "2.5.2");
define ("ODIOGO_WEDJE_ENABLED", FALSE); // Use "Widget Enabled DOM Javascript Embedding" (http://www.mikeindustries.com/blog/archive/2007/06/widget-deployment-with-wedje)

/*********** DO NOT EDIT UNLESS YOU REALLY NOW WHAT YOU ARE DOING ***********/
// Advanced Hacker Mode. NO SUPPORT PROVIDED FOR THESE OPTIONS.
$odiogo_adv_options['custom_listennow_text'] = "";
	// Replace the default "Listen Now" image with your own text link (String. Example: 'Podcast this'. No double quotes " allowed)
$odiogo_adv_options['custom_listennow_imageurl'] = "";
	// Replace the default "Listen Now" image with your own image (String. Absolute link like 'http://site.com/images/listen.gif')
$odiogo_adv_options['manually_insert_listennow_link'] = false;
	// Don't automatically insert the "Listen Now" button in post. Set to true if you want to manually add
	// the template tag odiogo_listennow() in your theme files (must be in The Loop)
/***************************************************************************/

// Add an admin page for the plugin
function odiogo_admin_menu () {
	add_options_page (odiogo_l10n('Odiogo Listen Button Options'), odiogo_l10n('Odiogo Listen Button'), 8, __FILE__, 'odiogo_plugin_options' );
}


// Display the plugin options management form
function odiogo_plugin_options () {
	// Update from old versions of the plugin if needed
	if (get_option('odiogo_feed_id') or get_option('odiogo_subscribe_button_title')) {
		$array = array(
			'odiogo_feed_id' => get_option('odiogo_feed_id'),
			'odiogo_subscribe_button_title' => get_option('odiogo_subscribe_button_title'),
		);
		odiogo_update_options($array);
		delete_option('odiogo_feed_id');
		delete_option('odiogo_subscribe_button_title');
		wp_cache_flush(); odiogo_init_options(); // refresh query cache ?
		$msg = odiogo_l10n('Options updated from old version of the plugin.');
	}

	// Process POST data if applicable
	if (isset($_POST['odiogo_form1']) and $_POST['odiogo_form1'] == 'odiogo_form1') {
		$msg = odiogo_process_post_form();
	}

	if (isset($msg) && !empty($msg))
		echo '<div class="updated fade" id="message"><p><strong>'.$msg.'</strong></p></div>';

	$odiogo_feed_id = odiogo_get_option('odiogo_feed_id');
	$error_msg = empty ($odiogo_feed_id) ? odiogo_l10n('You need to specify your Odiogo Feed ID.').'<br/>' : "";

	echo '
	<div class="wrap">
	<form method="post">
	';
	odiogo_nonce_field(odiogo_nonce());

	echo '
	<h2>'.odiogo_l10n('Odiogo Listen Button Options').'</h2>

	<table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">

	<tr valign="top">
		<th width="25%" scope="row">'.odiogo_l10n('Your Odiogo Feed ID:').'</th>
		<td>
			<p>
				<label for="odiogo-feed-id">
					<input type="text" id="odiogo-feed-id" name="odiogo_feed_id" size="5" value="'. $odiogo_feed_id  . '">
						<a href="http://www.odiogo.com/sign_up.php">'.odiogo_l10n('Don\'t have an Odiogo Feed ID? Get It Here.').'</a>
					<span style="color:red">' . $error_msg . '</span>
						<br/>
						<i>'.odiogo_l10n('(This is the number indicated in the activation email. Please <a href="http://www.odiogo.com/company_contact_us.php">contact us</a> if you don\'t find it)').'</i>
				</label>
			</p>
		</td>
	</tr>
	';

	$ping_rpc = (odiogo_get_option('odiogo_ping_rpc') === false) ? '' : 'checked="checked"';
	$safe_include = (odiogo_get_option('odiogo_safe_include') === false) ? '' : 'checked="checked"';

	echo '
	<tr valign="top">
		<th scope="row">'.odiogo_l10n('Odiogo Update Service:').'</th>
		<td>
			<p>
				<label for="odiogo-ping-rpc">
					<input id="odiogo-ping-rpc" name="odiogo_ping_rpc" value="true" '.$ping_rpc.' type="checkbox"> '.odiogo_l10n('Notify Odiogo servers when I write a new post').'
					<br/>
					<i>'.odiogo_l10n('(It\'s highly recommended to enable this option for faster availability of the audio files of your site)').'</i>
				</label>
			</p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">'.odiogo_l10n('Safe Include:').'</th>
		<td>
			<p>
				<label for="odiogo-safe-include">
					<input id="odiogo-safe-include" name="odiogo_safe_include" value="true" '.$safe_include.' type="checkbox"> '.odiogo_l10n('Enable safe include for custom WordPress template').'
					<br/>
					<i>'.odiogo_l10n('(It\'s important to enable this option if you\'re using a custom WordPress template which does not call wp_head or wp_footer. If unsure leave this option enabled.)').'</i>
				</label>
			</p>
		</td>
	</tr>
	';

	echo '
	</tbody>
	</table>
	<input type="hidden" name="odiogo_form1" value="odiogo_form1">
	<p class="submit"><input type="submit" name="submit" value="'.odiogo_l10n('Save &raquo;').'"></p>
	</form>
	';

	echo '
	<p style="text-align:center">Plugin version ' . ODIOGO_VERSION . ' &mdash; <a href="http://www.odiogo.com/">Odiogo Website</a> &mdash; <a href="http://blog.odiogo.com/">Odiogo Blog</a></p>
	';

	echo '
	</div>
	';
}


// Process values posted through the plugin options page
function odiogo_process_post_form () {
	check_admin_referer(odiogo_nonce());

	global $odiogo_options;
	$odiogo_options['odiogo_feed_id'] = intval(attribute_escape($_POST['odiogo_feed_id']));
	$odiogo_options['odiogo_ping_rpc'] = (bool) attribute_escape($_POST['odiogo_ping_rpc']);
	$odiogo_options['odiogo_safe_include'] = (bool) attribute_escape($_POST['odiogo_safe_include']);
	odiogo_update_options($odiogo_options);

	odiogo_modify_rpc_ping ($odiogo_options['odiogo_ping_rpc']);

	return odiogo_l10n('Options saved.');
}


// Add or remove Odiogo's RPC server to the list of notified servers
function odiogo_modify_rpc_ping ($add) {
	$odiogo_rpc = 'http://rpc.odiogo.com/ping/';
	$newservers = $servers = trim(get_option('ping_sites'));

	if ($add) {
		if (strpos($servers, $odiogo_rpc) === false)
			$newservers .= "\n" . $odiogo_rpc;
	} else {
		$newservers = preg_replace("/$odiogo_rpc\n?/", '', $servers);
	}

	if ($servers != $newservers) update_option('ping_sites', $newservers);
}


// Nonce wrapper for legacy compatibility
if ( !function_exists('wp_nonce_field') ) {
	function odiogo_nonce_field($action = -1) { return; }
	function odiogo_nonce() { return -1; }
} else {
	function odiogo_nonce_field($action = -1) { return wp_nonce_field($action); }
	function odiogo_nonce() { return 'odiogo-update'; }
}


// Are we currently viewing a "Page" ? Returns boolean
function odiogo_is_wp_page_static ($id) {
	return (in_array( $id, get_all_page_ids() ));
}


// Translation wrapper
function odiogo_l10n ($str) {
	return __($str, 'odiogolistenbutton');
}


// Save Odiogo options into the database
function odiogo_update_options($array) {
	update_option('odiogo_options', $array);
}


// Return specific option
function odiogo_get_option ($option) {
	global $odiogo_options;
	return $odiogo_options[$option];
	/* as of version 2.1, valid options are:
		- odiogo_feed_id (integer)
		- odiogo_subscribe_button_title (string)
		- odiogo_ping_rpc (bool)
		- odiogo_safe_include (bool)
	*/
}


// Widget administration: title update and form
function odiogo_subscribe_button_control () {
	if ( isset($_POST['odiogo_subscribe_button-submit']) ) {
		global $odiogo_options;
		$odiogo_options['odiogo_subscribe_button_title'] = attribute_escape ($_POST['odiogo_subscribe_button-title']);
		odiogo_update_options($odiogo_options);
	}

	echo '<p>';
	echo odiogo_l10n('Title: ');
	echo '<input id="odiogo_subscribe_button-title" name="odiogo_subscribe_button-title" type="text" value="' . odiogo_get_option('odiogo_subscribe_button_title') . '">';
	echo '<input type="hidden" id="odiogo_subscribe_button-form" name="odiogo_subscribe_button-submit" value="1">';
	echo '</p>';

}


// Load stuff on WP init
function odiogo_init() {
	odiogo_init_sidebar(); 	// init sidebar widgets
	odiogo_init_l10n();		// load translation file
	odiogo_init_options();	// init options
}

// Read options from database
function odiogo_init_options() {
	global $odiogo_options;
	$odiogo_options = get_option('odiogo_options');	// read options
}


// Init sidebar widgets (if theme is widget aware)
function odiogo_init_sidebar () {
	if (function_exists ('register_sidebar_widget') && function_exists ('register_widget_control')) {
		register_sidebar_widget (odiogo_l10n('Odiogo Subscribe Button'), 'odiogo_subscribe_button');
		register_widget_control (odiogo_l10n('Odiogo Subscribe Button'), 'odiogo_subscribe_button_control');
	}
}


// Load the translation file if present
function odiogo_init_l10n() {
	load_plugin_textdomain('odiogolistenbutton', 'wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/translations');
}


$global_odiogo_include_once = 0;

function odiogo_add_placeholders ($content, $filter = true) {
	global $id;
	global $global_odiogo_include_once;

	$odiogo_feed_id = odiogo_get_option ('odiogo_feed_id');

	// Do nothing on feeds, on Pages (as in "Pages != Posts") and if no Odiogo Feed ID has been set
	if (is_feed () || odiogo_is_wp_page_static ($id) || empty ($odiogo_feed_id))
		return $content;

	$str_post_title = get_the_title ($id);
	$str_post_title = str_replace ('"', '', $str_post_title);
	$str_post_title = str_replace ("'", '', $str_post_title);

	$odiogo_div = '';
	$odiogo_div .= odiogo_get_option('odiogo_safe_include') && $global_odiogo_include_once == 0 ? odiogo_listen_now_js_code () : '';

	if (ODIOGO_WEDJE_ENABLED)
	{
		$odiogo_div .=
		'
		<div class="odiogo_placeholder" title="'.$str_post_title.'" id="odiogo_placeholder_'.$id.'"></div>
		';
	}
	else
	{
		$odiogo_div .=
		'
		<!-- BEGIN ODIOGO LISTEN BUTTON v' . ODIOGO_VERSION . ' (WP) -->
		<script type="text/javascript" language="javascript">showOdiogoReadNowButton ("'. $odiogo_feed_id .'", "' . $str_post_title . '", "' . $id . '", 290, 55);</script>
		<br/>
		<script type="text/javascript" language="javascript">showInitialOdiogoReadNowFrame ("'. $odiogo_feed_id .'", "' . $id . '", 290, 0);</script>
		<!-- END ODIOGO LISTEN BUTTON v' . ODIOGO_VERSION . ' (WP) -->
		';
	}

	$global_odiogo_include_once++;

	if (!$filter)
		return $odiogo_div;

	return $odiogo_div . "\n". $content;
}


// Template tag for manual insertion of the "Listen Now" button
function odiogo_listennow() {
	echo odiogo_add_placeholders('', false);
}

// Display "Subscribe to this podcast" div placeholder (via widget or standalone template tag)
function odiogo_subscribe_button ($args = array() ) {
	$do_widget = ($args) ? true : false;

	$odiogo_subscribe = "";
	if (ODIOGO_WEDJE_ENABLED)
	{
		$odiogo_div = '<div class="odiogo_subscribe_placeholder" id="odiogo_subscribe_placeholder">'.$odiogo_subscribe.'</div>';
	}
	else
	{
		$odiogo_div =
		'
		<!-- BEGIN ODIOGO LISTEN BUTTON v' . ODIOGO_VERSION . ' (WP) -->
		<script type="text/javascript" language="javascript">showOdiogoSubscribeButton (_odiogo_directory_name);</script>
		<!-- END ODIOGO LISTEN BUTTON v' . ODIOGO_VERSION . ' (WP) -->
		';
	}

	// Output the placeholder div
	if ($do_widget) {
		echo $args['before_widget'];
		echo $args['before_title'] . odiogo_get_option('odiogo_subscribe_button_title') . $args['after_title'];
	}
	echo $odiogo_div;
	if ($do_widget)
		echo $args['after_widget'];
}

// Add main javascript file
function odiogo_listen_now_js_code ()
{
	$result = "";
	// no javascript to add if there is no feed ID
	$odiogo_feed_id = odiogo_get_option ('odiogo_feed_id');
	if (! empty ($odiogo_feed_id))
	{
		echo
		'
		<!-- BEGIN ODIOGO LISTEN BUTTON v' . ODIOGO_VERSION . ' (WP) -->
		<script type="text/javascript" language="javascript" src="http://widget.odiogo.com/odiogo_js.php?feed_id=' . $odiogo_feed_id . '&platform=wp&version=' . ODIOGO_VERSION . '"></script>
		' . (ODIOGO_WEDJE_ENABLED ? '<script type="text/javascript" language="javascript" src="http://widget.odiogo.com/odiogo.js"></script>' : '') . '
		<!-- END ODIOGO LISTEN BUTTON v' . ODIOGO_VERSION . ' (WP) -->
		';
	}
	return $result;
}

// Add main javascript file
function odiogo_listen_now_js ()
{
	if (! odiogo_get_option ('odiogo_safe_include'))
	{
		echo odiogo_listen_now_js_code ();
	}
}

// Set up the "Advanced Hacker Options"
function odiogo_advanced_options_js() {
	global $odiogo_adv_options;
	$str = '';

	if (isset($odiogo_adv_options['custom_listennow_text']) and !empty($odiogo_adv_options['custom_listennow_text']))
		$str .= '_odiogo_listen_button_text_link = "' . $odiogo_adv_options['custom_listennow_text'] . '"' . "\n";
	if (isset($odiogo_adv_options['custom_listennow_imageurl']) and !empty($odiogo_adv_options['custom_listennow_imageurl']))
		$str .= '_odiogo_listen_button_image_url = "' . $odiogo_adv_options['custom_listennow_text'] . '"' . "\n";

	if (!empty($str))
		echo '<script type="text/javascript" language="javascript">'.$str.'</script>';
}

// Plug everything in
add_action ('init', 'odiogo_init');
add_action ('admin_menu', 'odiogo_admin_menu');
if ($odiogo_adv_options['manually_insert_listennow_link'] !== true)
{
	add_filter ('the_content', 'odiogo_add_placeholders');
}

if (ODIOGO_WEDJE_ENABLED)
{
	add_action ('wp_footer', 'odiogo_listen_now_js');
}
else
{
	add_action ('wp_head', 'odiogo_listen_now_js');
}


if (
isset($odiogo_adv_options['custom_listennow_text']) and !empty($odiogo_adv_options['custom_listennow_text'])
or
isset($odiogo_adv_options['custom_listennow_imageurl']) and !empty($odiogo_adv_options['custom_listennow_imageurl'])
)
	add_action ('wp_footer', 'odiogo_advanced_options_js');

?>
