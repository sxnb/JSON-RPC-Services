<?php
/**
 * This is the endpoint for the JSON-RPC requests.
 * It initializes an object of type RequestEndpoint, which handles the request.
 */
require_once('config.php');
require_once('core\RequestEndpoint.php');

set_include_path(get_include_path() . PATH_SEPARATOR . $path);

// obtain the POST request data
$request = file_get_contents('php://input');

$requestEndpoint = new RequestEndpoint();
$requestEndpoint->handleRequest($request);