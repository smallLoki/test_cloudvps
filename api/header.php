<?php

ini_set('memory_limit', '-1');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
header("Content-Type: application/json; charset=utf-8");

date_default_timezone_set(@date_default_timezone_get());

function get_request_payload()
{
    return json_decode(file_get_contents('php://input'), true);
}