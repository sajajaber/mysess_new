<?php

trait Database
{
  private function connect()
  {
    $string = "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::ATTR_EMULATE_PREPARES => false,
    ];

    return new PDO($string, DBUSER, DBPASS, $options);
  }

  public function query($query, $data = [])
  {
    try {
      $con = $this->connect();
      $stm = $con->prepare($query);
      $stm->execute($data);

      $trimmedQuery = ltrim($query);
      if (preg_match('/^(select|show|pragma|describe|explain)/i', $trimmedQuery)) {
        $result = $stm->fetchAll();
        return is_array($result) && count($result) ? $result : [];
      }

      if (preg_match('/^insert/i', $trimmedQuery)) {
        return $con->lastInsertId() ?: $stm->rowCount();
      }

      return $stm->rowCount();
    } catch (PDOException $e) {
      error_log('Database query error: ' . $e->getMessage());
      return false;
    }
  }

  public function get_row($query, $data = [])
  {
    try {
      $con = $this->connect();
      $stm = $con->prepare($query);
      $stm->execute($data);
      $result = $stm->fetchAll();

      return is_array($result) && count($result) ? $result[0] : false;
    } catch (PDOException $e) {
      error_log('Database query error: ' . $e->getMessage());
      return false;
    }
  }
}
