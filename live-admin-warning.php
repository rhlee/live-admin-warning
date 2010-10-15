<?php
/*
Plugin Name: Live admin warning
Plugin URI: http://github.com/rhlee/live-admin-warning
Description: A plugin to warn you that you  are on a live Wordpress admin
Version: 0.1
Author: Richard H Lee
Author URI: http://github.com/rhlee
License: GPL
*/


//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once(ABSPATH . WPINC . '/pluggable.php');

$current_user = wp_get_current_user();

if(function_exists('get_usermeta'))
	$show = get_usermeta($current_user->ID, 'live-admin-warning-show');
else
	$show = get_user_meta($current_user->ID, 'live-admin-warning-show', true);

if($show == '1')
	add_action('admin_notices', "live_admin_warning");

if(!class_exists('live_admin_warning_class'))
{

class live_admin_warning_class
{
	private $uid;
	
	function live_admin_warning_class()
	{
		$current_user = wp_get_current_user();
		$this->uid = $current_user->ID;
	}
	
	function get_options()
	{
		$default_options = array(
			'live-admin-warning-show' => '1',
			'live-admin-warning-message' => 'Warning! This is a live site!'
		);
		
		foreach($default_options as $key => $value)
		{
			if(!$this->get_user_meta($this->uid, $key))
				$this->update_user_meta($key, $value);
		}
	}
	
	function get_user_meta($uid, $key)
	{
		if(function_exists('get_usermeta'))
			$show = get_usermeta($uid, $key);
		else
			$show = get_user_meta($uid, $key, true);
	}
	
	function update_user_meta($uid, $key, $val)
	{
		if(function_exists('update_usermeta'))
			$show = update_usermeta($uid, $key, $val);
		else
			$show = update_user_meta($uid, $key, $val);
	}
}

}

if(class_exists('live_admin_warning_class'))
	$live_admin_warning = new live_admin_warning_class();

if(isset($live_admin_warning))
{
	
}

if(!function_exists('LiveAdminWarning_ap'))
{
	function LiveAdminWarning_ap()
	{
		global $live_admin_warning;
		
		if(!isset($live_admin_warning))
			return;
		
		if (function_exists('add_submenu_page'))
			add_submenu_page(
				'options-general.php', "Live Admin Warning",
				"Live Admin Warning", 0, basename(__FILE__),
				array(&$live_admin_warning, 'printSettingsPage'
			));
	}
}

add_action('admin_menu', 'LiveAdminWarning_ap'); 

function live_admin_warning()
{ ?>
<link type="text/css" href="<?php echo plugins_url('live-admin-warning/css/live-admin-warning.css'); ?>" rel="stylesheet" />
<div id="live-admin-warning">
	Test
</div>
<?php }
?>
