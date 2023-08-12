<?php

define('OAUTH2_CLIENT_ID', '1138704876428349552');

$redirectURL = 'https://easyaivoice.com/disocrd_responce';

$params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => $redirectURL,
    'response_type' => 'code',
    'scope' => 'identify email'
);

// Redirect the user to Discord's authorization page
header('Location: https://discord.com/api/oauth2/authorize' . '?' . http_build_query($params));
die();
