<?php
require_once 'User.php';
require_once 'WalkingTour.php';
if (session_id() == '')
  session_start();
DefaultController::request($_POST);

class DefaultController {

  private static function validateMethod($data) {
    $result = true;

    $parts = explode('/', $data);
    if (! isset($parts[0]) || ! class_exists($parts[0]))
      throw new Exception('Class not found');
    elseif (! isset($parts[1]) || ! method_exists($parts[0], $parts[1]))
      throw new Exception('Method not found');

    return $result;
  }

  public static function request($data) {
    $validMethod = self::validateMethod($data['method']);
    if ($validMethod === true) {

      $parts = explode('/', $data['method']);
      $class = $parts[0];
      $method = $parts[1];

      echo json_encode($class::$method($data['data']));
    }
  }
}