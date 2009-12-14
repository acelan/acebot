<?php
/*
 *  convert.php -- a currency convert plugin for plurk bot
 *
 *      Copyright 2009 AceLan Kao(Chia-Lin Kao)
 *
 *  This file is subject to the terms and conditions of the GNU General Public
 *  License. See the file COPYING in the main directory of this archive for
 *  more details.
 */

require_once dirname(__FILE__).'/../../alplurkapi/al_plurk_api.php';

function plugin_convert_usage()
{
	return "ex. convert 1 USD to TWD";
}

function plugin_convert_re( $prefix= "")
{
	return "/^$prefix *: +(.+) +(.+) +(.+) +to +(.+$)/iU";
}

function plugin_convert( &$plurk_api, &$argv)
{
	$from= $argv[ 3];
	$to= $argv[ 4];
	$amount= $argv[ 2];

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
?>
