<?php

class Model
{
  use Database;

  protected $table = '';
  protected $limit = 10;
  protected $offset = 0;
  protected $order_column = 'id';
  protected $order_type = 'DESC';
  protected $allowedColumns = [];

  public function findAll()
  {
    $query = "SELECT * FROM {$this->table}
                  ORDER BY {$this->order_column} {$this->order_type}
                  LIMIT {$this->limit}
                  OFFSET {$this->offset}";

    return $this->query($query);
  }


  public function where($data, $data_not = [])
  {
    if (empty($data) && empty($data_not)) {
      return $this->findAll();
    }

    $query      = "SELECT * FROM $this->table WHERE ";
    $conditions = [];
    $params     = [];

    foreach ($data as $key => $value) {
      $conditions[]      = "$key = :eq_$key";   // namespaced to avoid key collisions
      $params["eq_$key"] = $value;
    }

    foreach ($data_not as $key => $value) {
      $conditions[]       = "$key != :neq_$key"; // namespaced to avoid key collisions
      $params["neq_$key"] = $value;
    }

    $query .= implode(" AND ", $conditions);
    return $this->query($query, $params);
  }

  public function first($data, $data_not = [])
  {
    if (empty($data) && empty($data_not)) {
      return false;
    }

    $query      = "SELECT * FROM $this->table WHERE ";
    $conditions = [];
    $params     = [];

    foreach ($data as $key => $value) {
      $conditions[]      = "$key = :eq_$key";
      $params["eq_$key"] = $value;
    }

    foreach ($data_not as $key => $value) {
      $conditions[]       = "$key != :neq_$key";
      $params["neq_$key"] = $value;
    }

    $query .= implode(" AND ", $conditions);
    $query .= " LIMIT 1";

    $result = $this->query($query, $params);
    if (is_array($result) && count($result)) {
      return $result[0];
    }

    return false;
  }

  public function insert($data)
  {
    $keys   = array_keys($data);
    $query  = "INSERT INTO $this->table ";
    $query .= "(" . implode(", ", $keys) . ")";
    $query .= " VALUES (:" . implode(", :", $keys) . ")";
    return $this->query($query, $data);
  }

  public function update($id, $data, $id_column = 'id')
  {
    $query  = "UPDATE $this->table SET ";
    $keys   = [];
    $params = [];

    foreach ($data as $key => $value) {
      $keys[]             = "$key = :col_$key";
      $params["col_$key"] = $value;
    }

    $query .= implode(", ", $keys);
    $query .= " WHERE $id_column = :where_id";
    $params['where_id'] = $id;
    return $this->query($query, $params);
  }

  public function delete($id, $id_column = 'id')
  {
    $query          = "DELETE FROM $this->table WHERE $id_column = :id";
    $params['id']   = $id;
    return $this->query($query, $params);
  }
}
