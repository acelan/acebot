<?php
/*
 *  translate.php -- a google translate plugin for plurk bot
 *
 *      Copyright 2009 AceLan Kao(Chia-Lin Kao)
 *
 *  This file is subject to the terms and conditions of the GNU General Public
 *  License. See the file COPYING in the main directory of this archive for
 *  more details.
 */

require_once dirname(__FILE__).'/../../alplurkapi/al_plurk_api.php';

function plugin_translate_usage()
{
	return "ex. translate en|zh_TW How are you?";
}

function plugin_translate_re( $prefix= "")
{
	return "/^$prefix *: +(.+) +(.+) +(.+$)/iU";
}

function plugin_translate( &$plurk_api, &$argv)
{
	$fromto= $argv[ 2];
	$str = $argv[ 3];

	$google_url= "http://www.google.com/translate_t";
	$array_query = array(
		'ie'		=> "UTF-8",
		'oe'		=> "UTF-8",
		'text'		=> "$str",
		'langpair'	=> "$fromto",
	);

	$plurk_api->http_get( $google_url, $array_query);
	$response= $plurk_api->http_response();

	$re = "/<span title=(.*)>(.+)<\/span>/U";
	preg_match( $re, $response['body'], $match);

	return $match[ 2];
}
?>
