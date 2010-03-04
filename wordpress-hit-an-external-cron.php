<?php
/*
Plugin Name: Hit an External Cron
Plugin URI: http://aaron.jorb.in/2010/wordpress-external-cron-plugin/
Description: Use wordpress's internal cron system to hit an external cron on a daily basis
Author: Aaron Jorbin
Version: 0.1
Author URI: http://aaron.jorb.in/
License: GPL2
*/

function install_jorbin_cron(){
	wp_schedule_event(time(), 'daily', 'jorbin_daily_event');
}
register_activation_hook(__FILE__, 'install_jorbin_cron');

function uninstall_jorbin_cron(){
	$timestamp = wp_next_scheduled( 'jorbin_daily_event');
	wp_unschedule_event($timestamp, 'jorbin_daily_event');
}
register_deactivation_hook(__FILE__, 'uninstall_jorbin_cron');



/* Add the Menu page */
add_action('admin_menu', 'jorbin_cron_settings_menu');

function jorbin_cron_settings_menu(){
	add_options_page('External Cron Settings', 'External Cron Settings', 'manage_options', 'cron', 'jorbin_cron_options_page');
}

function jorbin_cron_options_page(){
	echo "<div>";
	echo "<h2>Hit an External Cron Settings</h2>";
	echo '<form action="options.php" method="post">';
	settings_fields('jorbin_cron_options');
	do_settings_sections('cron');
	echo '<input name="Submit" type="submit" value="'. esc_attr('Save Changes') .'" />
</form></div>';
}


/* Fill the Menu page with content */

function jorbin_cron_init(){
	register_setting( 'jorbin_cron_options', 'jorbin_cron_options', 'jorbin_cron_options_validate' );
	add_settings_section('the_jorbin_cron', '', 'jorbin_cron_details_text', 'cron');
	add_settings_field('jorbin_cron_field', 'URL', 'jorbin_cron_field_display', 'cron', 'the_jorbin_cron');
}
add_action('admin_init', 'jorbin_cron_init');


function jorbin_cron_field_display(){
	$options = get_option('jorbin_cron_options');
	echo "<input id='jorbin_cron_field' name='jorbin_cron_options[url]' size='40' type='text' value='{$options['url']}' />";
}

function jorbin_cron_details_text(){
	echo "<p>Enter the URL you need to hit once a day</p>";
}
function jorbin_cron_options_validate($input){
	$newinput['url'] = esc_url_raw( trim( $input['url'] ) );
	return $newinput;
}

/* The Guts of the plugin
 * Where the cron actually happens
*/
function jorbin_cron(){
	$options = get_option('jorbin_cron_options');
	$url = $options['url'];
	$request = new WP_Http;
	$result = $request->request( $url );
}
add_action('jorbin_daily_event', 'jorbin_cron');

?>
