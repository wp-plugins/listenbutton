<?php
/*
Plugin Name: Odiogo Listen Button
Plugin URI: http://www.odiogo.com/download/wordpress/plugin/odiogo_listen_button_latest.php
Description: Give your blog a voice! Add an "Odiogo Listen Now Button" so your readers can listen to your posts. <a href="http://www.odiogo.com/sign_up.php">Free Sign up</a>.
Author: Odiogo
Version: 1.7
Author URI: http://www.odiogo.com/
*/

function odiogo_get_plugin_version ()
{
	return "1.7";
}

function odiogo_is_wp_head_template_bug_fix_enabled ()
{
	return true;
}

function odiogo_get_wp_static_pages ()
{
	global $wpdb;

	// Retrieve the version of the installed WP
	// WP 2.1 defines the pages differently from 2.0
	$wp_version = substr (get_bloginfo ('version'), 0, 3);
	if ($wp_version >= "2.1")
	{
		$pages = $wpdb->get_results ("select id, post_title
						from $wpdb->posts
						where post_status = 'publish'
						and post_type = 'page'
						order by post_title asc", 'ARRAY_A');
	}
	else
	{
		$pages = $wpdb->get_results ("select id, post_title
						from $wpdb->posts
						where post_status = 'static'
						order by post_title asc", 'ARRAY_A');
	}
	return $pages;
}

function odiogo_is_wp_page_static ($id)
{
	$pages = odiogo_get_wp_static_pages ();
	$result = false;

	if ($pages)
	{
		foreach ($pages as $page)
		{
			if ($page['id'] == $id)
			{
				return true;
			}
		}
	}
	return $result;
}

function odiogo_get_option_odiogo_feed_id ()
{
	return get_option ('odiogo_feed_id');
}

function odiogo_get_option_odiogo_subscribe_button_title ()
{
	return get_option ('odiogo_subscribe_button_title');
}

function odiogo_plugin_options ()
{
	$odiogo_feed_id = trim (odiogo_get_option_odiogo_feed_id ());
	$error_msg = strlen ($odiogo_feed_id) == 0 ? "You need to specify your Odiogo Feed ID.<br>" : "";

	$str =
'
<div class="wrap">
<h2>Odiogo Listen Button</h2>
<h2>Options</h2>
<form method="post">
<p>
<font color=red>' . $error_msg . '</font>
<b>Your Odiogo Feed ID:</b> <input type="text" name="odiogo_feed_id" size="5" value="'. $odiogo_feed_id  . '">
<a target=_blank href="http://www.odiogo.com/sign_up.php">Don\'t have an Odiogo Feed ID? Get It Here.</a>
<br>
(This is the number indicated in the activation email. Please <a target=_blank href="http://www.odiogo.com/company_contact_us.php">contact us</a> if you don\'t find it)
<br>
</p>
<input type="hidden" name="odiogo_form1" value="odiogo_form1">
<p><input type="submit" name="submit" value="Save  &raquo;"></p>
</form>
</div>
';

	echo $str;
}

function odiogo_listen_admin_menu ()
{
	add_options_page ('Odiogo Listen Button Options', 'Odiogo Listen Button', 8, __FILE__, 'odiogo_plugin_options');
}

function odiogo_listen_now_js ()
{
	$str =
	'
	<!-- BEGIN listen button odiogo.com v' . odiogo_get_plugin_version () . ' (WP) #1 -->
	<script type="text/javascript" language="javascript" src="http://widget.odiogo.com/odiogo_js.php?feed_id=' . odiogo_get_option_odiogo_feed_id () . '&platform=wp"></script>
	<!-- END listen button odiogo.com v' . odiogo_get_plugin_version () . ' (WP) #1 -->

	';
	return $str;
}

function odiogo_listen_now_1 ()
{
	$str = odiogo_listen_now_js ();
	echo $str;
}

function odiogo_listen_now_2 ()
{
	global $wp_query;
	$result = "";

	if (! odiogo_is_wp_page_static ($wp_query->post->ID))
	{
		$str_post_title = $wp_query->post->post_title;

		$str_post_title = str_replace ("\"", "", $str_post_title);
		$str_post_title = str_replace ("'", "", $str_post_title);

		$result = (odiogo_is_wp_head_template_bug_fix_enabled () ? odiogo_listen_now_js () : "") .
		'
		<!-- BEGIN listen button odiogo.com v' . odiogo_get_plugin_version () . ' (WP) #2 -->
		<script type="text/javascript" language="javascript">
		<!--
		showOdiogoReadNowButton ("'. odiogo_get_option_odiogo_feed_id () .'", "' . $str_post_title . '", "' . $wp_query->post->ID . '", 290, 55);
		//-->
		</script>
		<!-- END listen button odiogo.com v' . odiogo_get_plugin_version () . ' (WP) #2 -->

		';
	}
	return $result;
}

function odiogo_listen_now_3 ()
{
	global $wp_query;
	$result = "";

	if (! odiogo_is_wp_page_static ($wp_query->post->ID))
	{
		$result =
		'
		<!-- BEGIN listen button odiogo.com v' . odiogo_get_plugin_version () . ' (WP) #3 -->
		<script type="text/javascript" language="javascript">
		<!--
		showInitialOdiogoReadNowFrame ("'. odiogo_get_option_odiogo_feed_id () .'", "' . $wp_query->post->ID . '", 290, 0);
		//-->
		</script>
		<!-- END listen button odiogo.com v' . odiogo_get_plugin_version () . ' (WP) #3 -->

		';
	}
	return $result;
}

function odiogo_listen_content_filter ($content)
{
	if (is_feed ())
	{
		$result = $content;
	}
	else
	{
		$result = odiogo_listen_now_2 () . "<br>" . odiogo_listen_now_3 () . $content;
	}
	return $result;
}

function odiogo_process_post_form ()
{
	if ($_POST)
	{
		if ($_POST['odiogo_form1'])
		{
			update_option ('odiogo_feed_id', trim ($_POST['odiogo_feed_id']));
		}
	}
}

function odiogo_subscribe_button_control ()
{
	if ( $_POST['odiogo_subscribe_button-submit'] )
	{
		update_option ('odiogo_subscribe_button_title', trim ($_POST['odiogo_subscribe_button-title']));
	}

	echo '<p>';
	echo 'Title: ';
	echo '<input id="odiogo_subscribe_button-title" name="odiogo_subscribe_button-title" type="text" value="' . odiogo_get_option_odiogo_subscribe_button_title () . '">';
	echo '<input type="hidden" id="odiogo_subscribe_button-form" name="odiogo_subscribe_button-submit" value="1">';
	echo '</p>';

}

function odiogo_subscribe_button ($args)
{
	$str =
'
<ul>
<li>
<!-- BEGIN CODE ODIOGO SUBSCRIBE BUTTON -->
' . odiogo_listen_now_js () . '
<script type="text/javascript" language="javascript">
<!--
showOdiogoSubscribeButton (_odiogo_directory_name);
//-->
</script>
<!-- END CODE ODIOGO SUBSCRIBE BUTTON -->
</li>
</ul>
';
	echo $args['before_widget'];
	echo $args['before_title'] . odiogo_get_option_odiogo_subscribe_button_title () . $args['after_title'];
	echo $str;
	echo $args['after_widget'];
}

function odiogo_init_sidebar ()
{
	if (function_exists ('register_sidebar_widget') && function_exists ('register_widget_control'))
	{
		register_sidebar_widget ('Odiogo Subscribe Button', 'odiogo_subscribe_button');
		register_widget_control ('Odiogo Subscribe Button', 'odiogo_subscribe_button_control');
	}
}

add_action ('plugins_loaded', 'odiogo_init_sidebar');
add_action ('admin_menu', 'odiogo_listen_admin_menu');
add_action ('wp_head', 'odiogo_listen_now_1');
add_filter ('the_content', 'odiogo_listen_content_filter');

odiogo_process_post_form ();

?>