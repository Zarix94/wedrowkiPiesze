<?php
require_once 'Result.php';
require_once 'DBase.php';

class User {

  public $id;

  public static function isLoggedUser() {
    return isset($_SESSION['user']) && $_SESSION['user'] != null;
  }

  private static function isLoginUnique($login): bool {
    $db = DBase::getInstance();
    $countRes = $db->query('SELECT count(*) FROM users WHERE login =' . DBase::esc($login));

    return isset($countRes[0]['count']) && intval($countRes[0]['count']) == 0;
  }

  private static function isEmailUnique($email): bool {
    $db = DBase::getInstance();
    $countRes = $db->query('SELECT count(*) FROM users WHERE email =' . DBase::esc($email));

    return isset($countRes[0]['count']) && intval($countRes[0]['count']) == 0;
  }

  private static function validateRegisterUser(array $data): Result {
    $result = new Result();
    if (! isset($data['login']) || $data['login'] == '')
      $result->setError('Pole "Login" nie może być puste');
    elseif (strlen($data['login']) > 50)
      $result->setError('Pole "Login" może zawierać maksymalnie 50 znaków');
    elseif (! self::isLoginUnique($data['login']))
      $result->setError('Podany login istnieje już w systemie');
    elseif (! isset($data['password']) || $data['password'] == '')
      $result->setError('Pole "Hasło" nie może być puste');
    elseif (strlen($data['password']) < 6)
      $result->setError('Pole "Hasło" musi składać się z conajmniej 6 znaków');
    elseif (! isset($data['repeatPassword']) || $data['repeatPassword'] == '')
      $result->setError('Pole "Powtórz hasło" nie może być puste');
    elseif ($data['password'] != $data['repeatPassword'])
      $result->setError('Wprowadzone hasła się różnią');
    elseif (! isset($data['email']) || $data['email'] == '')
      $result->setError('Pole "E-mail" nie może być puste');
    elseif (! self::isEmailUnique($data['email']))
      $result->setError('Podany adres e-mail istnieje już w systemie');

    return $result;
  }

  private static function insertUser(array $data, String $hashPassword) {
    $query = '';
    $query .= 'INSERT INTO users ( ';
    $query .= '  login, ';
    $query .= '  password, ';
    $query .= '  email ';
    $query .= ') VALUES ( ';
    $query .= '  ' . DBase::esc($data['login']) . ', ';
    $query .= '  ' . DBase::esc($hashPassword) . ', ';
    $query .= '  ' . DBase::esc($data['email']) . ' ';
    $query .= ') ';

    $db = DBase::getInstance();
    $db->query($query);
  }

  public static function registerUser(array $data): Result {
    $result = self::validateRegisterUser($data);
    if ($result->getResult()) {
      $hashPassword = hash('sha512', $data['password']);
      self::insertUser($data, $hashPassword);
    }

    return $result;
  }

  private static function validateLogin(array $data): Result {
    $result = new Result();

    if (! isset($data['login']) || $data['login'] == '')
      $result->setError('Pole "Login" nie może być puste');
    elseif (! isset($data['password']) || $data['password'] == '')
      $result->setError('Pole "Hasło" nie może być puste');
    return $result;
  }

  private static function getUser(String $login, String $password): ?User {
    $user = null;

    $db = DBase::getInstance();

    $result = $db->query('SELECT * FROM users WHERE login = ' . DBase::esc($login) . ' AND password = ' . DBase::esc($password));
    if (count($result) > 0) {
      $user = new User();
      $user->id = $result[0]['id'];
    }

    return $user;
  }

  public static function login(array $data): Result {
    $result = self::validateLogin($data);
    if ($result->getResult()) {

      $hashPassword = hash('sha512', $data['password']);
      $_SESSION['user'] = self::getUser($data['login'], $hashPassword);
      if ($_SESSION['user'] == null)
        $result->setError('Nie znaleziono użytkownika');
    }
    return $result;
  }
}