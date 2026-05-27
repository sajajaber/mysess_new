<?php

class SessionMiddleware
{
  public static function check()
  {
    // Check if session is valid
    if (!isset($_SESSION['user_id'])) {
      return false;
    }

    // Check if session expired (2 hours)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
      self::destroy();
      return false;
    }

    // Update last activity
    $_SESSION['last_activity'] = time();

    return true;
  }

  public static function destroy()
  {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"]);
    }

    session_destroy();
  }
}
  