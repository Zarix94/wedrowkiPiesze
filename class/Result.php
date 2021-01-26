<?php

class Result {

  public $result = true;

  public $errors = [];

  public function getErrors() {
    return $this->errors;
  }

  public function setError($errorComunicate) {
    $this->result = false;
    $this->errors[] = $errorComunicate;
  }

  public function getResult() {
    return $this->result;
  }
}

?>