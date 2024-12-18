<?php

class DB
{
  public $servername = "localhost";
  public $username = "root";
  public $password = "";
  public $dbname = "iot";
  public $conn;

  public function __construct()
  {
    $this->conn = new mysqli(
      $this->servername,
      $this->username,
      $this->password,
      $this->dbname
    );

    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
  }

  public function insert($temperature, $humidity)
  {
    date_default_timezone_set('Asia/Jakarta');
    $timestamp = date('d/m/Y H:i:s');
    if ($temperature !== null && $humidity !== null && $timestamp !== null) {
      $sql = "INSERT INTO sensors_data (temperature, humidity, timestamp) VALUES (?, ?, ?)";

      $stmt = $this->conn->prepare($sql);

      if ($stmt === false) {
        die("Error preparing statement: " . $this->conn->error);
      }

      $stmt->bind_param("sss", $temperature, $humidity, $timestamp);

      if ($stmt->execute()) {
        return "New record created successfully!";
      } else {
        return "Error: " . $stmt->error;
      }

      $stmt->close();
    } else {
      return "Missing parameters in the URL!";
    }
  }

  public function getSensorsData()
  {
    $sql = "SELECT * FROM sensors_data ORDER BY id DESC LIMIT 20";
    $result = $this->conn->query($sql);

    if ($result === false) {
      return "Error: " . $this->conn->error;
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
      $data[] = $row;
    }

    return $data;
  }
}
