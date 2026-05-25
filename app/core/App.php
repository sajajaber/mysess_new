<?php
class App
{
  public $controller = 'Nurse';
  public $method = 'index';

  private function splitURL()
  {
    $URL = $_GET['url'] ?? '';
    $URL = trim($URL, '/');
    $URL = explode('/', $URL);

    return $URL;
  }

  public function loadController()
  {
    $URL = $this->splitURL();
    $controllerName = isset($URL[0]) && $URL[0] !== '' ? preg_replace('/[^a-zA-Z0-9_]/', '', $URL[0]) : '';
    $this->controller = $controllerName !== '' ? ucfirst($controllerName) : 'Nurse';

    $filename = __DIR__ . "/../controllers/" . $this->controller . ".php";

    if (file_exists($filename)) {
      require $filename;
    } else {
      $filename = __DIR__ . "/../controllers/_404.php";
      require $filename;
      $this->controller = "_404";
    }

    $controller = new $this->controller;
    $methodName = isset($URL[1]) ? preg_replace('/[^a-zA-Z0-9_]/', '', $URL[1]) : '';

    if ($methodName !== '' && method_exists($controller, $methodName)) {
      $this->method = $methodName;
      $params = array_slice($URL, 2);
    } elseif ($methodName === '') {
      $this->method = 'index';
      $params = [];
    } else {
      $filename = __DIR__ . "/../controllers/_404.php";
      require $filename;
      $controller = new _404;
      $this->method = 'index';
      $params = [];
    }

    call_user_func_array([$controller, $this->method], $params);
  }
}
