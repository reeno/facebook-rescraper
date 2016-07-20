<?php
/*
Plugin Name: Facebook Rescraper
Plugin URI: https://github.com/reeno/facebook-rescraper
Description: Tells Facebook to rescrape a posted long link to fetch the current preview image, description etc.
Version: 0.1
Author: Reeno
Author URI: https://github.com/reeno
*/

// Register plugin admin page
yourls_add_action('plugins_loaded', 'reeno_facebook_rescraper_add_page');
function reeno_facebook_rescraper_add_page() {
	yourls_register_plugin_page('facebook_rescraper', 'Facebook Rescraper', 'reeno_facebook_rescraper_display_page');
}

// Display admin page
function reeno_facebook_rescraper_display_page() {
	// Check if a form was submitted
	if (isset($_POST['APP_ID']) && isset($_POST['APP_SECRET'])) {
		reeno_facebook_rescraper_update_options($_POST['APP_ID'], $_POST['APP_SECRET']);
	}
	// Get value from database
	$facebook_rescraper_options = yourls_get_option('facebook_rescraper');
	
?>

<h2>Facebook Rescraper</h2>
<p>Tells Facebook to rescrape a posted long link to fetch the current preview image, description etc. So you don't have to do it manually via the <a href="https://developers.facebook.com/tools/debug/" target="_blank">Debug Tool</a>.</p>

<p>Enter your credential from the <a href="https://developers.facebook.com/apps/" target="_blank">Facebook App Dashboard</a> here.</p>
	<form method="post">
		<dl>
			<dt><label for="facebook_rescraper_options_app_id"><?php yourls_e('Facebook App ID'); ?></label></dt>
			<dd><input name="APP_ID" type="text" id="facebook_rescraper_options_app_id" value="<?php echo $facebook_rescraper_options['app_id']; ?>" /></dd>
			<dt><label for="facebook_rescraper_options_app_secret"><?php yourls_e('Facebook App Secret'); ?></label></dt>
			<dd><input name="APP_SECRET" type="password" id="facebook_rescraper_options_app_secret" value="<?php echo $facebook_rescraper_options['app_secret']; ?>" /></dd>
		</dl>
				
		<input style="display:block;" type="submit" value="<?php yourls_e('Update Settings'); ?>">
	</form>

<?php	

}

// Update option in database
function reeno_facebook_rescraper_update_options($app_id, $app_secret) {
	$options = array(
		'app_id'	=> $app_id,
		'app_secret'	=> $app_secret
	);
	yourls_update_option('facebook_rescraper', $options);
}

yourls_add_action('post_add_new_link', 'reeno_facebook_rescraper_new_link');

function reeno_facebook_rescraper_new_link($args) { // $args = $url, $keyword, $title
	// add_new_link_already_stored submits not $args[1]
	if(empty($args[1])) {
		return;
	}
	
	$uri = YOURLS_SITE.'/'.$args[1];
	$facebook_rescraper_options = yourls_get_option('facebook_rescraper');
	
	$access_token = $facebook_rescraper_options['app_id'].'|'.$facebook_rescraper_options['app_secret'];
	// APP_ID|APP_SECRET

	$params = array(
		'id'	=> $uri,
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

