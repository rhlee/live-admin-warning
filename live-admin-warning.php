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

function live_admin_warning()
{ ?>
<link type="text/css" href="<?php echo plugins_url('live-admin-warning/css/live-admin-warning.css'); ?>" rel="stylesheet" />
<div id="live-admin-warning">
	Test
</div>
<?php }
?>
