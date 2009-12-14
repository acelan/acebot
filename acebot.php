<?php
/*
 *  acebot.php -- a php plurk bot
 *
 *      Copyright 2009 AceLan Kao(Chia-Lin Kao)
 *
 *  This file is subject to the terms and conditions of the GNU General Public
 *  License. See the file COPYING in the main directory of this archive for
 *  more details.
 */

require_once dirname(__FILE__).'/../config.php';
require_once dirname(__FILE__).'/../alplurkapi/al_plurk_api.php';

date_default_timezone_set('Asia/Taipei');

$plurk_api = new al_plurk_api( $api_key);
$plurk_api->login( $username, $password);

function help()
{
	return;
}

function response_plurk( $plurk)
{
	global $plurk_api;

	if( preg_match( "/^@acebot *: +(.+) +(.+) +(.+$)/iU", $plurk['content_raw'], $match))
	{
		if( $plurk['is_unread'] == 2) // muted
			return;

		$function_name= $match[ 1];
		$plugin_filename= dirname(__FILE__).'/plugin/'.$function_name.".php";

		if( file_exists( $plugin_filename))
		{
			include_once $plugin_filename;

			$plugin_func= "plugin_".$function_name;
			$plugin_re= "plugin_".$function_name."_re";

			if( !function_exists( $plugin_re) || 
				!function_exists( $plugin_func))
				return;

			preg_match( $plugin_re("@acebot"), $plurk['content_raw'], $argv);
			$plurk_api->plurk_response_add( $plurk['plurk_id'], 'says',  $plugin_func( $plurk_api, $argv));
		}
		else
			$plurk_api->plurk_response_add( $plurk['plurk_id'], 'says', 'yoyoyo (rock)');
	}
	$plurk_api->plurk_mute( $plurk['plurk_id']);
}

$plurks= $plurk_api->plurk_from();
$plurks= $plurks['plurks'];
//print_r( $plurks);

if( is_array( $plurks))
	array_walk( $plurks, 'response_plurk');
?>
