<?php
ini_set('memory_limit', '-1');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');


$short_url = str_replace('/s/', '', $_SERVER['REQUEST_URI']);
if (isset($short_url)) {
    include_once('../api/db/db.php');
    $result = null;

    if ($connection) {
        include_once('../api/services/UrlService.php');

        $urlService = new UrlService($connection, $db);
        $result = $urlService->getURL($short_url);
    }
    if ($result) {
        $redirectURL = $result;
    } else {
        $redirectURL = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/index.html';
    }
    header('Location: '.$redirectURL);
}
exit;
