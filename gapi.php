<?php


require_once __DIR__ . '/vendor/autoload.php';


define('APPLICATION_NAME', 'gmail-backup');
define('CREDENTIALS_PATH',  __DIR__ . '/creds.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');

// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/gmail-php-quickstart.json
define('SCOPES', implode(' ', array(
        Google_Service_Gmail::GMAIL_READONLY)
));

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfigFile(CLIENT_SECRET_PATH);
    $client->setAccessType('offline');

    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
        $accessToken = unserialize(file_get_contents($credentialsPath));
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->authenticate($authCode);

        // Store the credentials to disk.
        if(!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, serialize($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
    }
    $client->setAccessToken($accessToken);

    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($client->getRefreshToken());
        file_put_contents($credentialsPath, serialize($client->getAccessToken()));
    }
    return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Gmail($client);

// Print the labels in the user's account.
$user = 'me';
$results = $service->users_labels->listUsersLabels($user);

if (count($results->getLabels()) == 0) {
    print "No labels found.\n";
} else {
    print "Labels:\n";
    foreach ($results->getLabels() as $label) {
        printf("- %s\n", $label->getName());
    }
}

$msg_list = $service->users_messages->listUsersMessages($user);
$count = $msg_list->getResultSizeEstimate();

print "total messages: $count\n";

$msgs = $msg_list->getMessages();
//print_r($msgs[0]);
//die();
$msg = $msgs[0];

//print_r($msg);

$m = $service->users_messages->get($user,$msg['id'],[ 'format' => 'minimal']);

print_r($m);

//$m = $service->users_messages->get($user,$msg['id'],[ 'format' => 'raw']);
//$base64 = $m->getRaw();
//$raw = base64_decode(str_replace('_','/',str_replace('-','+',$base64)));
//file_put_contents('test.eml',$raw);



