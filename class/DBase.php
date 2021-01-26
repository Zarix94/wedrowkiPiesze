<?php

class DBase {

  private static $inst;

  private $conn;

  private function __construct() {
    $connString = "host=127.0.0.1 port=5432 dbname=wedrowki user=postgres password=q";
    $this->conn = pg_connect($connString);

    if ($this->conn === false)
      throw new Exception('PGSQL connection failed');
  }

  public static function getInstance() {
    if (self::$inst == null)
      self::$inst = new DBase();

    return self::$inst;
  }

  public function query($query) {
    $queryResult = pg_query($this->conn, $query);
    $result = pg_fetch_all($queryResult);

    if (pg_last_error($this->conn) != '' || pg_result_error($queryResult) != '')
      throw new Exception('Query failed: ' . $query);
    elseif ($result == false)
      $result = [];

    return $result;
  }

  public static function esc($val, $quotes = true) {
    $escaped = htmlspecialchars($val);

    if ($quotes === true)
      $escaped = '\'' . $escaped . '\'';

    return $escaped;
  }
}


