<?php
class App
{
  public $controller = 'Auth';
  public $method     = 'login';

  private function splitURL()
  {
    $URL = $_GET['url'] ?? '';
    $URL = trim($URL, '/');
    $URL = filter_var($URL, FILTER_SANITIZE_URL);
    $URL = explode('/', $URL);
    return $URL;
  }

  public function loadController()
  {
    $URL = $this->splitURL();

    $controllerName = isset($URL[0]) && $URL[0] !== ''
      ? preg_replace('/[^a-zA-Z0-9_]/', '', $URL[0])
      : 'Auth';

    $baseName = ucfirst($controllerName);

    if (file_exists(__DIR__ . "/../controllers/" . $baseName . "Controller.php")) {
      $this->controller = $baseName . "Controller";
    } elseif (file_exists(__DIR__ . "/../controllers/" . $baseName . ".php")) {
      $this->controller = $baseName;
    } else {
      require __DIR__ . "/../controllers/_404.php";
      $controller = new _404;
      $controller->index();
      return;
    }

    require __DIR__ . "/../controllers/" . $this->controller . ".php";
    $controller = new $this->controller;

    $methodName = isset($URL[1]) && $URL[1] !== ''
      ? $URL[1]
      : ($baseName === 'Auth' ? 'login' : ($baseName === 'Nurse' ? 'dashboard' : 'index'));

    //convert - to underscores
    $methodName = str_replace('-', '_', $methodName);
    //strip anything that isn't a PHP identifier character
    $methodName = preg_replace('/[^a-zA-Z0-9_]/', '', $methodName);

    if (method_exists($controller, $methodName)) {
      $this->method = $methodName;
      $params = array_slice($URL, 2);
    } else {
      require __DIR__ . "/../controllers/_404.php";
      $controller = new _404;
      $this->method = 'index';
      $params = [];
    }

    call_user_func_array([$controller, $this->method], $params);
  }
}