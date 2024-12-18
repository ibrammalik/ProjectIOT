<?php
require("./vendor/autoload.php");
require("./DB.php");

use Bluerhinos\phpMQTT;

$server   = 'x2.revolusi-it.com';
$port     = 1883;
$username = 'usm';
$password = 'usmjaya001';
$client_id = 'client-G.231.22.0023_Web-Subscribe';

$mqtt = new phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, $username, $password)) {
  exit(1);
}

$temperature =  $mqtt->subscribeAndWaitForMessage('G.231.22.0023/temperature', 0);
$humidity =  $mqtt->subscribeAndWaitForMessage('G.231.22.0023/humidity', 0);

$mqtt->close();

$DB = new DB();
$db_message = $DB->insert($temperature, $humidity);
$sensors_data = $DB->getSensorsData();

header('Content-Type: application/json');

$response = json_encode([
  "temperature" => $temperature,
  "humidity" => $humidity,
  "db_message" => $db_message,
  "sensors_data" => $sensors_data
]);

echo $response;
