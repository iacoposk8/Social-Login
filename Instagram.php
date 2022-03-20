<?php
require __DIR__ . '/vendor/autoload.php';
require "config.php";

$provider = new League\OAuth2\Client\Provider\Instagram([
    'clientId'          => $config["instagram"]["id"],
    'clientSecret'      => $config["instagram"]["secret"],
    'redirectUri'       => $actual_link,
    'host'              => 'https://api.instagram.com',  // Optional, defaults to https://api.instagram.com
    'graphHost'         => 'https://graph.instagram.com' // Optional, defaults to https://graph.instagram.com
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getNickname());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}