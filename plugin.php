<?php
/*
Plugin Name: Facebook Scraper
Plugin URI: http://your-own-domain-here.com/articles/hey-test-my-sample-plugin/
Description: This plugin does something and something else
Version: 0.1
Author: Reeno
Author URI: http://...
*/


// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

yourls_add_action('post_add_new_link', 'reeno_facebook_scraper');

function reeno_facebook_scraper($args) {
	$access_token = 'APP_ID|APP_SECRET';

	$params = array(
		'id'	=> $args[0],
		'scrape'=> 'true',
		'access_token'	=> $access_token
	);
	
	$ch = curl_init("https://graph.facebook.com");
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER=>true,
		CURLOPT_SSL_VERIFYHOST=>false,
		CURLOPT_SSL_VERIFYPEER=>false,
		CURLOPT_POST=>true,
		CURLOPT_POSTFIELDS=>$params
	));
	$result = curl_exec($ch);
}

