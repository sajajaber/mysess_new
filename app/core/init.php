<?php

spl_autoload_register(function ($classname) {
  // check for models
  $modelFile = __DIR__ . "/../models/" . $classname . ".php";
  if (file_exists($modelFile)) {
    require $modelFile;
    return;
  }

  // Check for controllers
  $controllerFile = __DIR__ . "/../controllers/" . $classname . ".php";
  if (file_exists($controllerFile)) {
    require $controllerFile;
    return;
  }
});

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/Database.php';
require __DIR__ . '/Model.php';
require __DIR__ . '/Controller.php';
require __DIR__ . '/App.php';
