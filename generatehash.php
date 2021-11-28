<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              bmsc.ws/
 * @since             1.0.0
 * @package           Generatehash
 *
 * @wordpress-plugin
 * Plugin Name:       GenerateHash
 * Plugin URI:        bmsc.ws/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Yousry
 * Author URI:        bmsc.ws/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       generatehash
 * Domain Path:       /languages
 */





function GenerateHash( WP_REST_Request $request ) {
	$mid = "MID-7431-484"; 
	$secret = "7b9be793-ffdc-4002-972d-69d0476faf7d";  // 0226a0bc-3c3a-499c-ad42-25e266bfc90d     7b9be793-ffdc-4002-972d-69d0476faf7d
	 $Get_parameters = $request->get_json_params();
	$orderId = $Get_parameters['orderId'];
	$amount = $Get_parameters['amount'];
    $currency = $Get_parameters['currency'];

	$path = "/?payment=".$mid.".".$orderId.".".$amount.".".$currency;
     $hash = hash_hmac( 'sha256' , $path , $secret ,false);
	 $hashObj = new stdClass();
	 $hashObj->hash = $hash;
	 $hashObj->merchantId =$mid;
	
	 
	 $Decode_hashObj = json_encode($hashObj);
	 $Decode_parameters = json_encode($Get_parameters);

 


	  $final_data =  array_merge(json_decode($Decode_hashObj, true), json_decode($Decode_parameters, true));




  $make_call = callAPI('POST', 'https://test-iframe.kashier.io/checkout/', json_encode($final_data));
  return $response = json_decode($make_call, true);
  $errors   = $response['response']['errors'];
  $data     = $response['response']['data'][0];


  }


  function callAPI($method, $url, $data){
	$curl = curl_init();
	switch ($method){
	   case "POST":
		  curl_setopt($curl, CURLOPT_POST, 1);
		  if ($data)
			 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		  break;
	   case "PUT":
		  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		  if ($data)
			 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
		  break;
	   default:
		  if ($data)
			 $url = sprintf("%s?%s", $url, http_build_query($data));
	}
	// OPTIONS:
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	   'APIKEY: 111111111111111111111',
	   'Content-Type: application/json',
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	// EXECUTE:
	$result = curl_exec($curl);
	if(!$result){die("Connection Failure");}
	curl_close($curl);
	return $result;
 }



add_action( 'rest_api_init', function () {
	register_rest_route( 'generatehash/v1', '/pay', array(
	  'methods' => 'POST',
	  'callback' => 'GenerateHash',
	  'permission_callback' => '__return_true',
	) );
  } );
