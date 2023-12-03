<?php
include_once('../header.php');
/*______________________________________________________________________________________________*/

if (isset($_POST)) {
    include_once('../db/db.php');
    $result = null;

    if ($connection) {
        $data = get_request_payload();
        if (isset($data['id'])) {
            include_once('../services/UrlService.php');
            $urlService = new UrlService($connection, $db);
            $result = $urlService->deleteUrl($data['id']);
        }
    }

    echo json_encode($result);
}
exit;