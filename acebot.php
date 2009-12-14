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

require '../config.php';
require '../alplurkapi/al_plurk_api.php';

date_default_timezone_set('Asia/Taipei');

$plurk_api = new al_plurk_api( $api_key);
$plurk_api->login( $username, $password);

$funcs= array(
		array("help", "example", help, 0),
		array("convert", "convert 1 USD to TWD", convert, 4),
		array("translate", "translate en|zh_TW How are you?", translate, 2),
	);

function help()
{
	global $plurk_api, $plurks;

	for( $i= 1; $i < count( $func); $i++)
		$plurk_api->plruk_response_add( $plurk['plurk_id'], 'says', $func[ $i][ 0] . " - " . $func[ $i][ 1]);
		
	return;
}

function convert( $from, $to, $amount)
{
	global $plurk_api;

	$yahoo_money_url= "http://tw.money.yahoo.com/currency_exc_result";
	$array_query= array(
		'amt'	=> $amount,
		'from'	=> $from,
		'to'	=> $to,
	);

	$plurk_api->http_get( $yahoo_money_url, $array_query);
	$response= $plurk_api->http_response();

	$re = "/exponent\">.*經過計算後， (.+)<div/smU";
	preg_match( $re, $response['body'], $match);
	$result= preg_replace( '/<em>(.*)<\/em>(.*)<em>(.*)<\/em>(.*$)/U', '$1$2$3$4', $match[ 1]);

	return $result;
}

function translate( $fromto, $str)
{
	global $plurk_api;

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

function response_plurk( $plurk)
{
	global $plurk_api;

	if( preg_match( "/^@acebot *: +(.+) +(.+) +(.+$)/iU", $plurk['content_raw'], $match))
	{
		if( $plurk['is_unread'] == 2) // muted
			return;

		if( $match[1] == 'translate')
		{
			$plurk_api->plurk_response_add( $plurk['plurk_id'], 'says', translate( $match[ 2], $match[ 3]));
		}
		else if( $match[1] == 'convert')
		{
			preg_match( "/^@acebot *: +(.+) +(.+) +(.+) +to +(.+$)/iU", $plurk['content_raw'], $mm);
			$from= $mm[ 3];
			$to= $mm[ 4];
			$amount= $mm[2];

			$plurk_api->plurk_response_add( $plurk['plurk_id'], 'says', convert( $from, $to, $amount));
		}
		else
			$plurk_api->plurk_response_add( $plurk['plurk_id'], 'says', 'yoyoyo (rock)');
		$plurk_api->plurk_mute( $plurk['plurk_id']);
	}
}

$plurks= $plurk_api->plurk_from();
$plurks= $plurks['plurks'];
//print_r( $plurks);

if( is_array( $plurks))
	array_walk( $plurks, 'response_plurk');
?>
