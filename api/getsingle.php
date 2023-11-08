<?php
//read nominees
//Headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header("HTTP/1.1 200 OK");
die();
}

include_once '../config/db.php';
include_once '../src/translator.php';

$database = new Connection();
$db = $database->getConnection();

$incidents = new Translato($db);

include_once '../src/errors.php';
$error = new Errors;

$incidents->to = isset($_GET['to'])? $_GET['to'] : die($error->missen('To is missen from request'));
$incidents->id = isset($_GET['id'])? $_GET['id'] : die($error->missen('Id is missen from request'));

$result = $incidents->getSingleIncident();

echo json_encode($result);

?>