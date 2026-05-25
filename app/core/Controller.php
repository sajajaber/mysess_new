<?php

class Controller
{
  public function view($name, $data = [])
  {
    if (is_array($data) && count($data)) {
      extract($data, EXTR_SKIP);
    }

    $filename = __DIR__ . "/../views/" . $name . ".php";

    if (file_exists($filename)) {
      require $filename;
    } else {
      require __DIR__ . "/../views/_404view.php";
    }
  }
}
