# Bugfix Requirements Document

## Introduction

MySESS is a custom PHP MVC framework for a special education school management system. Five bugs were identified across the framework's core files and controllers that collectively prevent the application from functioning: broken URL rewriting, a fatal PHP error in the router, a broken class autoloader, a database connection typo, and controllers that double-execute on load. This document captures the defective behaviors, the correct behaviors that replace them, and the existing behaviors that must be preserved.

---

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN Apache processes any request to the application THEN the system fails to rewrite URLs because `public/.htaccess` contains `RewriteRu1e` (digit `1` instead of letter `l`), causing a 500 error or pass-through to the filesystem.

1.2 WHEN `App::loadController()` instantiates a controller THEN the system throws a fatal PHP error because `$this->$controller` is a variable-variable that dereferences an undefined dynamic property instead of reading the `$controller` property.

1.3 WHEN the SPL autoloader in `app/core/init.php` attempts to load a model class THEN the system fails to find the file because the path concatenation is missing the `/` directory separator and the `.` before `php`, producing a malformed path such as `../app/modelsMyClassphp`.

1.4 WHEN the application initialises a PDO database connection THEN the system fails to connect because `DBHOST` is defined as `'loacalhost'` (transposed letters) instead of `'localhost'`.

1.5 WHEN `App::loadController()` requires and instantiates `Nurse.php` or `Therapist.php` THEN the system executes the controller's `index()` method a second time because each controller file contains self-instantiation code (`$nurse = new Nurse(); $nurse->index();`) at the bottom, causing double execution and duplicate output or side-effects.

---

### Expected Behavior (Correct)

2.1 WHEN Apache processes any request to the application THEN the system SHALL rewrite the URL correctly via `RewriteRule` (with the letter `l`) so that all non-file, non-directory requests are forwarded to `index.php?url=...`.

2.2 WHEN `App::loadController()` instantiates a controller THEN the system SHALL read the controller name from `$this->controller` (no variable-variable) and instantiate the correct class without a fatal error.

2.3 WHEN the SPL autoloader in `app/core/init.php` attempts to load a model class THEN the system SHALL construct the path as `__DIR__ . "/../models/" . $classname . ".php"` (with correct `/` separator and `.php` extension) so that the file is found and required successfully.

2.4 WHEN the application initialises a PDO database connection THEN the system SHALL connect successfully because `DBHOST` is defined as `'localhost'` (correct spelling).

2.5 WHEN `App::loadController()` requires and instantiates `Nurse.php` or `Therapist.php` THEN the system SHALL execute the controller action exactly once, because no self-instantiation code exists at the bottom of those controller files.

---

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a valid URL segment maps to an existing controller and method THEN the system SHALL CONTINUE TO route the request to the correct controller method and render the expected view.

3.2 WHEN a URL segment maps to a non-existent controller THEN the system SHALL CONTINUE TO fall back to the `_404` controller and render the 404 view.

3.3 WHEN a logged-in nurse accesses any nurse route THEN the system SHALL CONTINUE TO enforce the role guard and allow access, loading models and rendering views correctly.

3.4 WHEN a logged-in therapist accesses any therapist route THEN the system SHALL CONTINUE TO enforce the role guard and allow access, rendering the therapist view correctly.

3.5 WHEN an unauthenticated user accesses a protected route THEN the system SHALL CONTINUE TO redirect to the login page.

3.6 WHEN the autoloader is invoked for a controller class THEN the system SHALL CONTINUE TO locate and require the correct file from `app/controllers/`.

3.7 WHEN a model method executes a database query THEN the system SHALL CONTINUE TO connect via PDO and return the expected result set or row count.

---

## Bug Condition Pseudocode

### Bug Condition Functions

```pascal
FUNCTION isBugCondition_1(request)
  // Triggers when any HTTP request is processed by Apache
  INPUT: request of type HttpRequest
  OUTPUT: boolean
  RETURN htaccess contains "RewriteRu1e"  // digit 1 instead of letter l
END FUNCTION

FUNCTION isBugCondition_2(url)
  // Triggers when the router tries to instantiate a controller
  INPUT: url of type ParsedURL
  OUTPUT: boolean
  RETURN App.php uses "$this->$controller"  // variable-variable
END FUNCTION

FUNCTION isBugCondition_3(classname)
  // Triggers when autoloader tries to resolve a model class
  INPUT: classname of type string
  OUTPUT: boolean
  RETURN init.php path omits "/" separator and "." before "php"
END FUNCTION

FUNCTION isBugCondition_4(connection)
  // Triggers on every database connection attempt
  INPUT: connection of type DBConfig
  OUTPUT: boolean
  RETURN DBHOST = 'loacalhost'
END FUNCTION

FUNCTION isBugCondition_5(controllerFile)
  // Triggers when Nurse.php or Therapist.php is required
  INPUT: controllerFile of type string (filename)
  OUTPUT: boolean
  RETURN controllerFile IN ['Nurse.php', 'Therapist.php']
         AND file contains self-instantiation at bottom
END FUNCTION
```

### Fix-Checking Properties

```pascal
// Property 1: URL Rewriting
FOR ALL request WHERE isBugCondition_1(request) DO
  result ← processRequest'(request)
  ASSERT result routes to index.php AND no 500 error
END FOR

// Property 2: Controller Instantiation
FOR ALL url WHERE isBugCondition_2(url) DO
  result ← loadController'(url)
  ASSERT result instantiates correct controller AND no fatal error
END FOR

// Property 3: Autoloader Path
FOR ALL classname WHERE isBugCondition_3(classname) DO
  result ← autoload'(classname)
  ASSERT file_exists(resolved_path) = true
END FOR

// Property 4: Database Host
FOR ALL connection WHERE isBugCondition_4(connection) DO
  result ← connect'(connection)
  ASSERT PDO connection succeeds
END FOR

// Property 5: Controller Self-Instantiation
FOR ALL controllerFile WHERE isBugCondition_5(controllerFile) DO
  result ← requireController'(controllerFile)
  ASSERT execution_count(action) = 1
END FOR
```

### Preservation Properties

```pascal
// For all inputs that do NOT trigger any of the above bug conditions:
FOR ALL X WHERE NOT isBugCondition_1(X)
             AND NOT isBugCondition_2(X)
             AND NOT isBugCondition_3(X)
             AND NOT isBugCondition_4(X)
             AND NOT isBugCondition_5(X) DO
  ASSERT F(X) = F'(X)  // fixed code behaves identically to original for non-buggy inputs
END FOR
```
