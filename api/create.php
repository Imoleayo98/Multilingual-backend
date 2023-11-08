<?php
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
//get Database
include_once '../config/db.php';
include_once '../src/translator.php';

$database = new Connection();
$db = $database->getConnection();

$incidents = new Translato($db);
//get data from php input
$data = json_decode(file_get_contents("php://input"));
//get errors
include_once '../src/errors.php';
$error = new Errors;
$incidents->title=isset($data->title)? $data->title : die($error->missen('title missen from request'));
$incidents->description=isset($data->description)? $data->description : die($error->missen('description missen from request'));
$incidents->to=isset($data->to)? $data->to : die($error->missen('to missen from request'));
$incidents->from=isset($data->from)? $data->from : die($error->missen('from missen from request'));

echo json_encode($incidents->createIncident());




