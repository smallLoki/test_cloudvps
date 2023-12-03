<?php
include_once('../header.php');
/*______________________________________________________________________________________________*/

if (isset($_POST)) {
    include_once('../db/db.php');
    $result = null;

    if ($connection) {
        include_once('../services/UrlService.php');

        $urlService = new UrlService($connection, $db);
        $result = $urlService->getUrlList();
    }

    echo json_encode($result);
}
exit;

