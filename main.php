<?php
// Include the TwitterAPI class
require_once('TwitterAPI.php');

// Set up your Twitter API credentials
$consumerKey = 'Your consumer key';
$consumerSecret = 'Your consumer secret key';
$accessToken = 'Your Access token';
$accessTokenSecret = 'Your access token secret';

// Your Twitter post
$text = "Hello world this is just a testing post";

// Create an instance of the TwitterAPI class
$twitterAPI = new TwitterAPI($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

// Post the tweet using the API
$response = $twitterAPI->postTweet($text);

// Display the API response
echo $response;
?>
