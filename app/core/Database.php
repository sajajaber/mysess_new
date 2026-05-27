<?php

trait Database
{
  private function connect()
  {
    $string  = "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4";
    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    return new PDO($string, DBUSER, DBPASS, $options);
  }

  public function query($query, $data = [])
  {
    try {
      $con = $this->connect();
      $stm = $con->prepare($query);

      // PDO cannot bind integer-only params (LIMIT / OFFSET) via execute().
      // We detect keys whose values are plain integers and bind them explicitly
      // with PARAM_INT so MySQL accepts them in LIMIT / OFFSET clauses.
      foreach ($data as $key => $value) {
        $paramKey = ':' . ltrim($key, ':');
        if (is_int($value)) {
          $stm->bindValue($paramKey, $value, PDO::PARAM_INT);
        } else {
          $stm->bindValue($paramKey, $value, PDO::PARAM_STR);
        }
      }

      $stm->execute();

      $trimmedQuery = ltrim($query);
      if (preg_match('/^(select|show|pragma|describe|explain)/i', $trimmedQuery)) {
        $result = $stm->fetchAll();
        return is_array($result) ? $result : [];   // return empty array, never false
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

      foreach ($data as $key => $value) {
        $paramKey = ':' . ltrim($key, ':');
        if (is_int($value)) {
          $stm->bindValue($paramKey, $value, PDO::PARAM_INT);
        } else {
          $stm->bindValue($paramKey, $value, PDO::PARAM_STR);
        }
      }

      $stm->execute();
      $result = $stm->fetchAll();

      return is_array($result) && count($result) ? $result[0] : false;

    } catch (PDOException $e) {
      error_log('Database query error: ' . $e->getMessage());
      return false;
    }
  }
}