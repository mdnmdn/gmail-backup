<?php
/**
 * Created by PhpStorm.
 * User: mdn
 * Date: 08/07/16
 * Time: 14:14
 */

require_once __DIR__ . '/vendor/autoload.php';

use Anod\Gmail\Imap;

print "start\n";

// https://developers.google.com/oauthplayground/

$email = "pippolaus@gmail.com";
$token = 'ya29.Ci8ZAxrZB8ctCfIt33fKI-QFDyeb53QOCW39t-F00r1VyWN7l8qobgH4j80qIOzUaQ';

//$msgId = \Anod\Gmail\Math::bchexdec("13db4f8141abcfbd"); // == "1430824723191418813"


print "accessing gmail...\n";
$debug = true;
$protocol = new \Anod\Gmail\Imap($debug);
$gmail = new \Anod\Gmail\Gmail($protocol);
//$gmail->setId("Example App","0.1","Alex Gavrishev","alex.gavrishev@gmail.com");

$gmail->connect();
$gmail->authenticate($email, $token);

print "request count...\n";

$folders = $gmail->getFolders();

foreach($folders as $folder){
    $gmail->selectFolder($folder);

    $count = $gmail->count();
    print " -> $folder ($count)\n";
}

$currentFolder = "INBOX";

$gmail->selectFolder($currentFolder);

$count = $gmail->count();

print "emails for $currentFolder: $count\n";

$folder = $gmail->getCurrentFolder();

$msg = $gmail->getMessage(1711);

print "id: " . $msg->

print_r($msg->getTopLines());