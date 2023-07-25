<?php

class TwitterAPI
{
    private $consumerKey;
    private $consumerSecret;
    private $accessToken;
    private $accessTokenSecret;

    public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->accessToken = $accessToken;
        $this->accessTokenSecret = $accessTokenSecret;
    }

    private function generateOAuthSignature($method, $url, $params)
    {
        // Generate OAuth parameters
        $oauthTimestamp = time();
        $oauthNonce = md5(uniqid(rand(), true));
        $oauthParameters = array(
            'oauth_consumer_key=' . $this->consumerKey,
            'oauth_nonce=' . $oauthNonce,
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $oauthTimestamp,
            'oauth_token=' . $this->accessToken,
            'oauth_version=1.0'
        );

        // Merge additional parameters with the OAuth parameters
        $oauthParameters = array_merge($oauthParameters, $params);

        // Sort the OAuth parameters
        sort($oauthParameters);

        // Create the OAuth parameter string
        $oauthParameterString = implode('&', $oauthParameters);

        // Create the OAuth base string for signing
        $oauthBaseString = $method . '&';
        $oauthBaseString .= urlencode($url) . '&';
        $oauthBaseString .= urlencode($oauthParameterString);

        // Create the OAuth signing key
        $oauthSigningKey = urlencode($this->consumerSecret) . '&' . urlencode($this->accessTokenSecret);

        // Generate the OAuth signature
        $oauthSignature = base64_encode(hash_hmac('sha1', $oauthBaseString, $oauthSigningKey, true));
        return urlencode($oauthSignature);
    }

    public function postTweet($text)
    {
        $url = 'https://api.twitter.com/2/tweets';

        // Create the Authorization header
        $params = array(
            'text=' . urlencode($text)
        );
        $oauthSignature = $this->generateOAuthSignature('POST', $url, $params);
        $authorizationHeader = 'OAuth ';
        $authorizationHeader .= 'oauth_consumer_key="' . $this->consumerKey . '", ';
        $authorizationHeader .= 'oauth_nonce="' . md5(uniqid(rand(), true)) . '", ';
        $authorizationHeader .= 'oauth_signature="' . $oauthSignature . '", ';
        $authorizationHeader .= 'oauth_signature_method="HMAC-SHA1", ';
        $authorizationHeader .= 'oauth_timestamp="' . time() . '", ';
        $authorizationHeader .= 'oauth_token="' . $this->accessToken . '", ';
        $authorizationHeader .= 'oauth_version="1.0"';

        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
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

        // Execute the cURL request
        $response = curl_exec($curl);

        // Close the cURL session
        curl_close($curl);

        // Return the API response
        return $response;
    }
}
?>
