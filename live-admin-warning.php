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
	private $uid, $options;
	
	function live_admin_warning_class()
	{
		$current_user = wp_get_current_user();
		$this->uid = $current_user->ID;
		$this->get_options();
	}
	
	function get_options()
	{
		$default_options = array(
			'live_admin_warning_show' => '0',
			'live_admin_warning_message' => 'Warning! This is a live site!'
		);
		
		$options = array();
		
		foreach($default_options as $key => $value)
		{
			$option = $this->get_user_meta($this->uid, $key);
			if($option)
				$options[$key] = $option;
			else
				$test = $this->update_user_meta($this->uid, $key, $value);
		}
		
		$this->options = empty($options)? $default_options : $options;
		
		return (empty($options)? $default_options : $options);
	}
	
	function get_option($key)
	{
		return $this->options[$key];
	}
	
	function get_user_meta($uid, $key)
	{
		if(function_exists('get_usermeta'))
			return get_usermeta($uid, $key);
		else
			return get_user_meta($uid, $key, true);
	}
	
	function update_user_meta($uid, $key, $val)
	{
		if(function_exists('update_usermeta'))
			return update_usermeta($uid, $key, $val);
		else
			return update_user_meta($uid, $key, $val);
	}
	
	function printSettingsPage()
	{
		if( $_POST['update_live_admin_warning_settings'] &&
		$_POST['live_admin_warning_message'] )
		{
			$this->update_user_meta(
				$this->uid,
				'live_admin_warning_show',
				(isset($_POST['live_admin_warning_show'])? '1' : '0')
			);
			
			$this->update_user_meta(
				$this->uid,
				'live_admin_warning_message',
				$_POST['live_admin_warning_message']
			);
		}
		
		$this->get_options();
		
?>
<div class=wrap>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<h2>Live Admin Warning</h2>
		<table class="form-table">

<tbody>
	<tr>
		<th>Show warning</th>
		<td><input type="checkbox" value="<?php echo $this->get_option('live_admin_warning_show'); ?>" id="live_admin_warning_show" name="live_admin_warning_show" <?php if($this->get_option('live_admin_warning_show') == '1')echo 'checked="checked" '; ?>/></td>
	<tr>
	</tr>
		<th>Warning message</th>
		<td><input type="text" class="regular-text" value="<?php echo $this->get_option('live_admin_warning_message'); ?>" id="live_admin_warning_message" name="live_admin_warning_message" /></td>
	</tr>
</tbody>

		</table>
		<div class="submit">
			<input type="submit" name="update_live_admin_warning_settings" value="Update Settings" />
		</div>
	</form>
</div>
<?php
	
	}
	
	function display()
	{ ?>
		<link type="text/css" href="<?php echo plugins_url('live-admin-warning/css/live-admin-warning.css'); ?>" rel="stylesheet" />
		<div id="live-admin-warning">
		       <?php echo $this->get_option('live_admin_warning_message'); ?>
		</div><?php
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
		
		if($live_admin_warning->get_option('live_admin_warning_show') == '1')
			add_action('admin_notices',
				array(&$live_admin_warning, 'display'));
	}
}

add_action('admin_menu', 'LiveAdminWarning_ap');
?>
