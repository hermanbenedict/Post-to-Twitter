<?php
$oauthTimestamp = time();
$oauthNonce = md5(uniqid(rand(), true));

$oauthParameters = array(
    'oauth_consumer_key=' . $consumerKey,
    'oauth_nonce=' . $oauthNonce,
    'oauth_signature_method=HMAC-SHA1',
    'oauth_timestamp=' . $oauthTimestamp,
    'oauth_token=' . $accessToken,
    'oauth_version=1.0'
);

sort($oauthParameters);

$oauthParameterString = implode('&', $oauthParameters);


$oauthBaseString = 'POST&';
$oauthBaseString .= urlencode('https://api.twitter.com/2/tweets') . '&';
$oauthBaseString .= urlencode($oauthParameterString);

$oauthSigningKey = urlencode($consumerSecret) . '&' . urlencode($accessTokenSecret);

$oauthSignature = base64_encode(hash_hmac('sha1', $oauthBaseString, $oauthSigningKey, true));
$oauthSignature = urlencode($oauthSignature);

$authorizationHeader = 'OAuth ';
$authorizationHeader .= 'oauth_consumer_key="' . $consumerKey . '", ';
$authorizationHeader .= 'oauth_nonce="' . $oauthNonce . '", ';
$authorizationHeader .= 'oauth_signature="' . $oauthSignature . '", ';
$authorizationHeader .= 'oauth_signature_method="HMAC-SHA1", ';
$authorizationHeader .= 'oauth_timestamp="' . $oauthTimestamp . '", ';
$authorizationHeader .= 'oauth_token="' . $accessToken . '", ';
$authorizationHeader .= 'oauth_version="1.0"';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twitter.com/2/tweets',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => '{
    "text": "' . $text . '"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: ' . $authorizationHeader
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>
