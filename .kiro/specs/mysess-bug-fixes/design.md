# MySESS Bug Fixes — Bugfix Design

## Overview

Five bugs in the MySESS PHP MVC framework collectively prevent the application from running at all. Each bug is a small, isolated defect: a typo in `.htaccess`, a variable-variable in the router, a malformed autoloader path, a misspelled database hostname, and self-instantiation code left at the bottom of two controller files. The fix strategy is strictly surgical — one targeted change per bug, no refactoring, no new abstractions.

---

## Glossary

- **Bug_Condition (C)**: The condition that identifies an input or code state that triggers a specific bug.
- **Property (P)**: The desired correct behavior when the bug condition holds after the fix is applied.
- **Preservation**: Existing behaviors that must remain byte-for-byte identical after the fix.
- **`App::loadController()`**: The method in `app/core/App.php` responsible for resolving a URL segment to a controller class and invoking the appropriate method.
- **`spl_autoload_register`**: The PHP autoloader callback in `app/core/init.php` that resolves class names to file paths for models.
- **`DBHOST`**: The constant defined in `app/core/config.php` used by `Database.php` to open a PDO connection.
- **Self-instantiation**: Lines at the bottom of a controller file that create a new instance of the class and call a method directly, outside of any framework dispatch.
- **Variable-variable (`$this->$controller`)**: A PHP construct where the property name is itself read from a variable, rather than being a literal identifier.

---

## Bug Details

### Bug 1 — `.htaccess` Typo (`RewriteRu1e`)

The bug manifests on every HTTP request processed by Apache. The directive `RewriteRu1e` (digit `1` in place of letter `l`) is not a recognised Apache directive, so URL rewriting is silently skipped or causes a 500 error. No request ever reaches `index.php`.

**Formal Specification:**
```
FUNCTION isBugCondition_1(request)
  INPUT: request of type HttpRequest
  OUTPUT: boolean

  RETURN htaccess_directive = "RewriteRu1e"   // digit 1, not letter l
END FUNCTION
```

**Examples:**
- `GET /nurse` → Apache cannot rewrite → 404 or 500 (expected: routed to `index.php?url=nurse`)
- `GET /auth/login` → same failure (expected: routed to `index.php?url=auth/login`)
- `GET /` → same failure (expected: routed to `index.php?url=`)

---

### Bug 2 — Variable-Variable in `App::loadController()` (`$this->$controller`)

The bug manifests when the router successfully finds a controller file and tries to record the controller name. The line `$this->$controller = ucfirst($URL[0])` uses a variable-variable: PHP reads the value of `$controller` (which is undefined at that point in the method) and tries to set a dynamic property with that name. This produces a fatal error or silently sets the wrong property, so `$this->controller` is never updated and the wrong class is instantiated.

**Formal Specification:**
```
FUNCTION isBugCondition_2(url)
  INPUT: url of type ParsedURL (array from splitURL)
  OUTPUT: boolean

  RETURN App_php_source contains "$this->$controller ="   // variable-variable
         AND url[0] maps to an existing controller file
END FUNCTION
```

**Examples:**
- `GET /nurse` → `$this->$controller` → fatal error / wrong property set (expected: `$this->controller = 'Nurse'`)
- `GET /therapist` → same failure (expected: `$this->controller = 'Therapist'`)
- `GET /auth` → same failure (expected: `$this->controller = 'Auth'`)

---

### Bug 3 — Malformed Autoloader Path in `init.php`

The bug manifests whenever PHP's autoloader is triggered for any model class. The original concatenation:

```php
require $classname = "../app/models". ucfirst($classname) ."php";
```

produces a path like `../app/modelsMyClassphp` — missing the `/` directory separator between `models` and the class name, and missing the `.` before `php`. The file is never found and a fatal error is thrown.

**Formal Specification:**
```
FUNCTION isBugCondition_3(classname)
  INPUT: classname of type string (PHP class name)
  OUTPUT: boolean

  path ← "../app/models" . ucfirst(classname) . "php"
  RETURN NOT file_exists(path)   // path is always malformed
END FUNCTION
```

**Examples:**
- Autoloading `User` → resolves to `../app/modelsUserphp` (expected: `../app/models/User.php`)
- Autoloading `Medication` → resolves to `../app/modelsMedicationphp` (expected: `../app/models/Medication.php`)
- Autoloading `Student` → resolves to `../app/modelsStudentphp` (expected: `../app/models/Student.php`)

---

### Bug 4 — Misspelled `DBHOST` (`'loacalhost'`)

The bug manifests on every PDO connection attempt. The constant `DBHOST` is defined as `'loacalhost'` (letters `o` and `a` transposed). PDO cannot resolve this hostname and throws a connection exception, making every database-backed operation fail.

**Formal Specification:**
```
FUNCTION isBugCondition_4(config)
  INPUT: config of type DBConfig
  OUTPUT: boolean

  RETURN config.DBHOST = 'loacalhost'   // transposed letters
END FUNCTION
```

**Examples:**
- Any model query → PDO throws `SQLSTATE[HY000] [2002] No such host` (expected: connection succeeds to `localhost`)

---

### Bug 5 — Self-Instantiation in `Nurse.php` and `Therapist.php`

The bug manifests when `App::loadController()` uses `require` to load either controller file. PHP executes the file top-to-bottom, so the lines at the bottom:

```php
$nurse = new Nurse();
$nurse->index();
```

run immediately on `require`, before the framework has a chance to dispatch. The controller action executes once here (with no session context, potentially crashing), and then executes again when the framework calls `call_user_func_array`. This causes double output, duplicate side-effects, or a fatal error on the first execution.

**Formal Specification:**
```
FUNCTION isBugCondition_5(controllerFile)
  INPUT: controllerFile of type string (filename)
  OUTPUT: boolean

  RETURN controllerFile IN ['Nurse.php', 'Therapist.php']
         AND file_bottom_contains_self_instantiation(controllerFile)
END FUNCTION
```

**Examples:**
- `require 'Nurse.php'` → `$nurse->index()` fires immediately, then fires again via `call_user_func_array` (expected: fires exactly once)
- `require 'Therapist.php'` → same double-execution (expected: fires exactly once)

---

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Valid URL segments that map to an existing controller and method SHALL continue to route correctly and render the expected view.
- URL segments that map to a non-existent controller SHALL continue to fall back to the `_404` controller.
- Role guards in `Nurse` and `Therapist` constructors SHALL continue to redirect unauthenticated or wrong-role users to the login page.
- Model queries via PDO SHALL continue to return the expected result sets and row counts.
- The autoloader SHALL continue to locate and require controller files from `app/controllers/` (this path is not changed).
- All existing method signatures, class hierarchies, view names, and session handling SHALL remain unchanged.

**Scope:**
All code paths that do not involve the five specific defective lines are completely unaffected by these fixes. This includes:
- All view files
- All model logic
- The `Database.php`, `Model.php`, `Controller.php` base classes
- The `Auth` controller
- All CSS, JS, and asset files

---

## Hypothesized Root Cause

1. **Bug 1 — Keystroke error**: The digit `1` and the letter `l` are visually similar. The directive was mistyped during initial authoring and not caught because Apache silently ignores unknown directives in some configurations.

2. **Bug 2 — PHP variable-variable confusion**: The developer intended `$this->controller` but wrote `$this->$controller`. In PHP, `$this->$controller` is valid syntax (it reads the property whose name equals the value of `$controller`), so no parse error is raised — the bug only surfaces at runtime when `$controller` is undefined or holds an unexpected value.

3. **Bug 3 — String concatenation oversight**: The developer omitted the `/` separator between the directory name and the class name, and omitted the `.` before `php`. Both are easy to miss when writing a long concatenation expression. The assignment inside `require` (`require $classname = ...`) also overwrites the `$classname` variable, which is a secondary issue.

4. **Bug 4 — Keystroke transposition**: `loacalhost` is a common transposition of `localhost`. The constant was defined once and never tested in isolation, so the error was not caught until a full connection attempt was made.

5. **Bug 5 — Development scaffolding left in place**: The self-instantiation lines were likely added during early development to test the controllers by running the file directly (`php Nurse.php`). They were never removed before the framework dispatch mechanism was wired up.

---

## Correctness Properties

Property 1: Bug Condition — URL Rewriting Restored

_For any_ HTTP request processed by Apache where the `.htaccess` bug condition holds (directive reads `RewriteRu1e`), the fixed `.htaccess` SHALL correctly rewrite the URL to `index.php?url=...` with no 500 error or pass-through to the filesystem.

**Validates: Requirements 2.1**

Property 2: Bug Condition — Controller Property Assignment

_For any_ URL where the router bug condition holds (`$this->$controller` variable-variable), the fixed `App::loadController()` SHALL assign the controller name to `$this->controller` and instantiate the correct class without a fatal error.

**Validates: Requirements 2.2**

Property 3: Bug Condition — Autoloader Path Resolution

_For any_ model class name where the autoloader bug condition holds (malformed path), the fixed autoloader SHALL construct the path as `__DIR__ . "/../models/" . ucfirst($classname) . ".php"` and successfully require the file.

**Validates: Requirements 2.3**

Property 4: Bug Condition — Database Connection

_For any_ PDO connection attempt where the `DBHOST` bug condition holds (`'loacalhost'`), the fixed config SHALL define `DBHOST` as `'localhost'` and the connection SHALL succeed.

**Validates: Requirements 2.4**

Property 5: Bug Condition — Single Controller Execution

_For any_ controller file where the self-instantiation bug condition holds (`Nurse.php` or `Therapist.php` with bottom-of-file instantiation), the fixed file SHALL execute the controller action exactly once when dispatched by the framework.

**Validates: Requirements 2.5**

Property 6: Preservation — Non-Buggy Inputs Unchanged

_For any_ input where none of the five bug conditions hold (isBugCondition_1 through isBugCondition_5 all return false), the fixed codebase SHALL produce exactly the same behavior as the original codebase, preserving all routing, rendering, session handling, and database behavior.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7**

---

## Fix Implementation

### Changes Required

All fixes are strictly minimal — one targeted change per bug.

---

**Bug 1**

**File**: `public/.htaccess`

**Specific Change**:
- Replace `RewriteRu1e` (digit `1`) with `RewriteRule` (letter `l`) on line 4.
- No other lines are touched.

---

**Bug 2**

**File**: `app/core/App.php`

**Function**: `loadController()`

**Specific Change**:
- Replace `$this->$controller = ucfirst($URL[0]);` with `$this->controller = ucfirst($URL[0]);`
- Remove the extra `$` before `controller`. One character change.

---

**Bug 3**

**File**: `app/core/init.php`

**Specific Change**:
- Replace:
  ```php
  require $classname = "../app/models". ucfirst($classname) ."php";
  ```
  with:
  ```php
  require __DIR__ . "/../models/" . ucfirst($classname) . ".php";
  ```
- Adds the `/` separator, adds `.` before `php`, and uses `__DIR__` so the path is correct regardless of the working directory from which PHP is invoked.

---

**Bug 4**

**File**: `app/core/config.php`

**Specific Change**:
- Replace `'loacalhost'` with `'localhost'` in the `DBHOST` constant definition.
- One character transposition corrected.

---

**Bug 5**

**File**: `app/controllers/Nurse.php`

**Specific Change**:
- Remove the two lines at the bottom of the file:
  ```php
  $nurse = new Nurse();
  $nurse->index();
  ```

**File**: `app/controllers/Therapist.php`

**Specific Change**:
- Remove the two lines at the bottom of the file:
  ```php
  $therapist = new Therapist();
  $therapist->index();
  ```

---

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate each bug on the unfixed code, then verify the fix works correctly and preserves existing behavior. Because these are infrastructure-level bugs (Apache config, PHP fatal errors, path resolution), most validation is done through unit and integration tests rather than property-based tests. Preservation checking uses property-based tests where the input space is large enough to warrant it.

---

### Exploratory Bug Condition Checking

**Goal**: Surface counterexamples that demonstrate each bug BEFORE implementing the fix. Confirm or refute the root cause analysis.

**Test Plan**: Write tests that simulate each bug condition against the unfixed code and assert the defective behavior is observable. Run on unfixed code to confirm failures match the hypothesized root cause.

**Test Cases**:
1. **`.htaccess` Directive Test**: Parse the `.htaccess` file and assert that the string `RewriteRu1e` is present (will pass on unfixed code, confirming the bug exists).
2. **Variable-Variable Test**: Instantiate `App`, call `loadController()` with a valid URL, and assert that `$this->controller` is set correctly (will fail on unfixed code with a fatal error or wrong value).
3. **Autoloader Path Test**: Invoke the autoloader with a known model class name and assert `file_exists` on the resolved path (will fail on unfixed code because the path is malformed).
4. **DBHOST Value Test**: Assert that `DBHOST === 'localhost'` (will fail on unfixed code where it equals `'loacalhost'`).
5. **Self-Instantiation Test**: Scan `Nurse.php` and `Therapist.php` source for the self-instantiation pattern and assert it is absent (will fail on unfixed code).

**Expected Counterexamples**:
- `.htaccess` contains `RewriteRu1e` — Apache directive not recognised
- `loadController()` throws `Undefined variable $controller` or sets wrong dynamic property
- Autoloader resolves `User` to `../app/modelsUserphp` — `file_exists` returns false
- `DBHOST` equals `'loacalhost'` — PDO connection fails
- `Nurse.php` bottom contains `$nurse = new Nurse()` — double execution on require

---

### Fix Checking

**Goal**: Verify that for all inputs where each bug condition holds, the fixed code produces the expected behavior.

**Pseudocode:**
```
FOR ALL request WHERE isBugCondition_1(request) DO
  result ← processRequest_fixed(request)
  ASSERT result routes to index.php AND no 500 error
END FOR

FOR ALL url WHERE isBugCondition_2(url) DO
  result ← loadController_fixed(url)
  ASSERT this->controller = ucfirst(url[0]) AND correct class instantiated
END FOR

FOR ALL classname WHERE isBugCondition_3(classname) DO
  result ← autoload_fixed(classname)
  ASSERT file_exists(__DIR__ . "/../models/" . ucfirst(classname) . ".php")
END FOR

FOR ALL config WHERE isBugCondition_4(config) DO
  result ← connect_fixed(config)
  ASSERT PDO connection succeeds
END FOR

FOR ALL controllerFile WHERE isBugCondition_5(controllerFile) DO
  result ← requireController_fixed(controllerFile)
  ASSERT execution_count(action) = 1
END FOR
```

---

### Preservation Checking

**Goal**: Verify that for all inputs where none of the bug conditions hold, the fixed code produces the same result as the original code.

**Pseudocode:**
```
FOR ALL X WHERE NOT isBugCondition_1(X)
             AND NOT isBugCondition_2(X)
             AND NOT isBugCondition_3(X)
             AND NOT isBugCondition_4(X)
             AND NOT isBugCondition_5(X) DO
  ASSERT F(X) = F'(X)
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking of the autoloader and router because:
- The input space (class names, URL segments) is large and varied
- It catches edge cases (unusual class names, deeply nested URLs) that manual tests miss
- It provides strong guarantees that no non-buggy path is broken

**Test Plan**: Observe behavior on unfixed code for non-buggy inputs, then write property-based tests capturing that behavior.

**Test Cases**:
1. **Routing Preservation**: Generate random valid URL segments that map to existing controllers and verify the router dispatches correctly after the fix.
2. **Autoloader Preservation (Controllers)**: Generate controller class names and verify the autoloader still finds them in `app/controllers/` after the init.php change.
3. **404 Fallback Preservation**: Verify that unknown URL segments still fall back to `_404` after the fix.
4. **Config Constants Preservation**: Verify that `ROOT`, `DBNAME`, `DBUSER`, `DBPASS`, and `APP_NAME` are unchanged after the `config.php` fix.
5. **Controller Method Preservation**: Verify that all public methods on `Nurse` and `Therapist` are still callable after removing the self-instantiation lines.

---

### Unit Tests

- Assert `public/.htaccess` contains `RewriteRule` (letter `l`) and does not contain `RewriteRu1e` (digit `1`)
- Assert `app/core/App.php` source does not contain `$this->$controller` (variable-variable pattern)
- Assert autoloader resolves each model class name to a path that `file_exists` returns true for
- Assert `DBHOST` constant equals `'localhost'`
- Assert `Nurse.php` and `Therapist.php` do not contain self-instantiation lines at file scope

### Property-Based Tests

- Generate arbitrary model class names from the set `{User, Medication, MedicationLog, HealthEvent, HealthRecord, Student, Nurse}` and verify the fixed autoloader resolves each to an existing file
- Generate arbitrary valid URL segments and verify `loadController()` sets `$this->controller` to the correct ucfirst value without fatal errors
- Generate arbitrary non-`localhost` hostnames and verify the config constant is not affected by the fix (i.e., only `DBHOST` changed, nothing else)

### Integration Tests

- Full request cycle: simulate `GET /nurse` and verify the Nurse controller's `index()` method is called exactly once and the correct view is rendered
- Full request cycle: simulate `GET /therapist` and verify the Therapist controller's `index()` method is called exactly once
- Full request cycle: simulate `GET /nonexistent` and verify the `_404` controller is invoked
- Database integration: verify a PDO connection can be opened using the fixed `DBHOST` value
- Autoloader integration: verify that requiring `init.php` and then instantiating `new User()` does not throw a fatal error
