<?php

// Set up your Twitter API credentials
$consumerKey = 'consumerKey';
$consumerSecret = 'consumerSecret';
$accessToken = 'accessToken';
$accessTokenSecret = 'accessTokenSecret';

// Define your tweet text and optional image path
$text = 'Your tweet text here';

$imagePath = 'image.jpg'; // Update this with the path to your image

if($imagePath ==""){
require "postText.php";
}
elseif($imagePath !=""&&$text !=""){
    require "postImage.php";
}
?>
