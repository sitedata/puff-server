<?php

////	Puff
// Puff is installed in the current directory.
$Sitewide['Puff']['Root']      = __DIR__.'/';
$Sitewide['Puff']['Cron']      = $Sitewide['Puff']['Root'].'cron/';
$Sitewide['Puff']['Hooks']     = $Sitewide['Puff']['Root'].'hooks/';
$Sitewide['Puff']['Settings']  = $Sitewide['Puff']['Root'].'settings/';
$Sitewide['Puff']['Functions'] = $Sitewide['Puff']['Root'].'../_functions/';
$Sitewide['Puff']['Libs']      = $Sitewide['Puff']['Root'].'../_libs/';



// TODO Move all functions to functions files.
require_once __DIR__.'/../_functions/glob_recursive.php';
function require_all_once($Directory) {
	global $Sitewide;
	foreach (glob_recursive($Directory.'*.php') as $File) {
		require_once $File;
	}
}
function puff_hook($Hook) {
	global $Sitewide;
	require_all_once($Sitewide['Puff']['Hooks'].$Hook.'/');
}
function ifOr($One, $Two, $Reference) {
	return !empty($One[$Reference]) ? $One[$Reference] : $Two[$Reference];
}
if ( $Sitewide['Settings']['AutoLoad']['Functions'] ) {
	require_all_once($Sitewide['Puff']['Functions']);
}



////	Require the Configuration
require_once $Sitewide['Puff']['Settings'].'core.php';
require_all_once($Sitewide['Puff']['Settings']);
if ( is_readable($Sitewide['Puff']['Root'].'settings.custom.php') ) {
	require_once $Sitewide['Puff']['Root'].'settings.custom.php';
}

////	Request
// Scheme & Security
if (
	isset($_SERVER['HTTPS']) &&
	$_SERVER['HTTPS'] != 'off'
) {
	$Sitewide['Request']['Scheme'] = 'https';
	$Sitewide['Request']['Secure'] = true;
} else {
	$Sitewide['Request']['Scheme'] = 'http';
	$Sitewide['Request']['Secure'] = false;
}
// Host
$Sitewide['Request']['Host'] = filter_input(INPUT_SERVER, 'HTTP_HOST');
if ( empty($Sitewide['Request']['Host']) ) {
	$Sitewide['Request']['Host'] = filter_input(INPUT_SERVER, 'SERVER_NAME');
}
// Paths, Queries and Fragments
if ( isset($_SERVER['REQUEST_URI']) ) {
	$Sitewide['Request']['Path'] = explode('#', $_SERVER['REQUEST_URI']);
} else {
	$Sitewide['Request']['Path'] = array('');
}
if ( isset($Sitewide['Request']['Path'][1]) ) {
	$Sitewide['Request']['Fragment'] = $Sitewide['Request']['Path'][1];
} else {
	$Sitewide['Request']['Fragment'] = false;
}
$Sitewide['Request']['Path'] = explode('?', $Sitewide['Request']['Path'][0]);
if ( isset($Sitewide['Request']['Path'][1]) ) {
	$Sitewide['Request']['Query'] = $Sitewide['Request']['Path'][1];
} else {
	$Sitewide['Request']['Query'] = false;
}
$Sitewide['Request']['Path'] = $Sitewide['Request']['Path'][0];
// Full Address
$Sitewide['Request']['Full'] = $Sitewide['Request']['Scheme'].'://'.$Sitewide['Request']['Host'].$Sitewide['Request']['Path'];
if ( $Sitewide['Request']['Query'] ) {
	$Sitewide['Request']['Full'] .= '?'.$Sitewide['Request']['Query'];
}
if ( $Sitewide['Request']['Fragment'] ) {
	$Sitewide['Request']['Full'] .= '#'.$Sitewide['Request']['Fragment'];
}

////	Frontend
// Assume the root is 1 level up.
$Sitewide['Root']                = realpath(__DIR__.'/../').'/';

////	Templates
$Sitewide['Templates']['Root']   = $Sitewide['Root'].'_templates/';
$Sitewide['Templates']['Header'] = $Sitewide['Templates']['Root'].'header.php';
$Sitewide['Templates']['Footer'] = $Sitewide['Templates']['Root'].'footer.php';

////	Assets
$Sitewide['Assets']['Internal']['Root']      = $Sitewide['Root'].'assets/';
$Sitewide['Assets']['Internal']['JS']        = $Sitewide['Assets']['Internal']['Root'].'js/';
$Sitewide['Assets']['Internal']['CSS']       = $Sitewide['Assets']['Internal']['Root'].'css/';
$Sitewide['Assets']['Internal']['Image']     = $Sitewide['Assets']['Internal']['Root'].'images/';
$Sitewide['Assets']['External']['Root']      = $Sitewide['Settings']['Site Root'].'assets/';
$Sitewide['Assets']['External']['JS']        = $Sitewide['Assets']['External']['Root'].'js/';
$Sitewide['Assets']['External']['CSS']       = $Sitewide['Assets']['External']['Root'].'css/';
$Sitewide['Assets']['External']['Image']     = $Sitewide['Assets']['External']['Root'].'images/';
$Sitewide['Assets']['Extension']['JS']       = '.js';
$Sitewide['Assets']['Extension']['CSS']      = '.css';
$Sitewide['Assets']['Extension']['Image']    = '.png';

////	Cookies
$Sitewide['Cookies']['HTTPOnly'] = true;
$Sitewide['Cookies']['Prefix'] = str_replace('.', '_', $Sitewide['Request']['Host']);

////	Timezone
date_default_timezone_set('UTC');
$Time = time();
$Date = date($Time, DATE_ATOM);

////	Preload Hook
puff_hook('preload');
// Load any page variables declared as $Page, just this one time.
if ( !empty($Sitewide['Page']) ) {
	$Page = $Sitewide['Page'];
}
if ( empty($Page['Images']) && !empty($Page['Image']) ) {
	$Page['Images'][] = $Page['Image'];
}
