<?php
$oauthTimestamp = time();
$oauthNonce = md5(uniqid(rand(), true));

$imageData = file_get_contents($imagePath);

$uploadUrl = 'https://upload.twitter.com/1.1/media/upload.json';

$oauthParametersUpload = array(
    'oauth_consumer_key=' . $consumerKey,
    'oauth_nonce=' . $oauthNonce,
    'oauth_signature_method=HMAC-SHA1',
    'oauth_timestamp=' . $oauthTimestamp,
    'oauth_token=' . $accessToken,
    'oauth_version=1.0'
);

sort($oauthParametersUpload);

$oauthParameterStringUpload = implode('&', $oauthParametersUpload);

$oauthBaseStringUpload = 'POST&';
$oauthBaseStringUpload .= urlencode($uploadUrl) . '&';
$oauthBaseStringUpload .= urlencode($oauthParameterStringUpload);

$oauthSigningKeyUpload = urlencode($consumerSecret) . '&' . urlencode($accessTokenSecret);

$oauthSignatureUpload = base64_encode(hash_hmac('sha1', $oauthBaseStringUpload, $oauthSigningKeyUpload, true));
$oauthSignatureUpload = urlencode($oauthSignatureUpload);

$authorizationHeaderUpload = 'OAuth ';
$authorizationHeaderUpload .= 'oauth_consumer_key="' . $consumerKey . '", ';
$authorizationHeaderUpload .= 'oauth_nonce="' . $oauthNonce . '", ';
$authorizationHeaderUpload .= 'oauth_signature="' . $oauthSignatureUpload . '", ';
$authorizationHeaderUpload .= 'oauth_signature_method="HMAC-SHA1", ';
$authorizationHeaderUpload .= 'oauth_timestamp="' . $oauthTimestamp . '", ';
$authorizationHeaderUpload .= 'oauth_token="' . $accessToken . '", ';
$authorizationHeaderUpload .= 'oauth_version="1.0"';

$curlUpload = curl_init();

curl_setopt_array($curlUpload, array(
    CURLOPT_URL => $uploadUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => array('media' => new CURLFile($imagePath)),
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $authorizationHeaderUpload
    ),
));

$responseUpload = curl_exec($curlUpload);
curl_close($curlUpload);

$responseUploadData = json_decode($responseUpload, true);
$mediaId = $responseUploadData['media_id_string'];

// Step 2: Create the tweet with the image (v2)
$oauthNonce = md5(uniqid(rand(), true));
$oauthTimestamp = time();

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
  CURLOPT_POSTFIELDS => json_encode(array(
      'text' => $text,
      'media' => array('media_ids' => array($mediaId))
  )),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: ' . $authorizationHeader
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>
