<?php
require("./vendor/autoload.php");

use Bluerhinos\phpMQTT;

$server   = 'x2.revolusi-it.com';
$port     = 1883;
$username = 'usm';
$password = 'usmjaya001';
$client_id = 'client-G.231.22.0023_Web-Publish';

function controlLED($ledStates)
{
  $payload = json_encode($ledStates);
  global $server, $port, $client_id, $username, $password;

  $mqtt = new phpMQTT($server, $port, $client_id);

  try {
    $mqtt->connect(true, NULL, $username, $password);
    $mqtt->publish('G.231.22.0023/control', $payload, 0, true);
    $mqtt->close();
    return ['success' => true, 'message' => 'LED states updated.', 'payload' => $payload];
  } catch (\Exception $e) {
    return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $inputData = file_get_contents('php://input');
  $ledStates = json_decode($inputData, true);

  if (!isset($ledStates['LED1']) || !isset($ledStates['LED2']) || !isset($ledStates['LED3'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
  }

  $response = controlLED($ledStates);
  echo json_encode($response);
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
