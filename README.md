# MySESS - My Special Education Support System

A full-stack web application built as a senior project for a special education center. MySESS provides a unified platform for managing student health, academic progress, staff assignments, and daily operations with role-based access for every user type in the school.

---

## Tech Stack

- **Backend:** PHP 8.2 (custom MVC, no framework)
- **Database:** MySQL via PDO
- **Frontend:** HTML, CSS, Vanilla JavaScript
- **Server:** Apache (XAMPP)
- **Version Control:** Git / GitHub

---

## Architecture

The project follows a clean MVC pattern built from scratch:

```
mysess_new/
├── public/              # Entry point (index.php), assets (CSS, JS)
├── app/
│   ├── core/            # Framework layer
│   │   ├── App.php      # URL router — maps /controller/method/param automatically
│   │   ├── Database.php # PDO trait with type-aware parameter binding
│   │   ├── Model.php    # Base ORM — insert, update, delete, where, first
│   │   ├── Controller.php
│   │   ├── config.php
│   │   └── init.php
│   ├── controllers/     # One controller per role
│   ├── models/          # One model per entity / role
│   └── views/           # PHP templates, layouts, components
└── auth/
```

### Key Design Decisions

**Custom ORM layer** — `Model.php` provides `insert()`, `update()`, `where()`, and `first()` using namespaced parameter prefixes (`eq_`, `neq_`, `col_`, `where_`) to avoid PDO key collisions when multiple conditions are built dynamically.

**Type-aware PDO binding** — `Database.php` inspects each parameter before binding. Integer values are bound with `PDO::PARAM_INT`, which is required for `LIMIT`/`OFFSET` clauses when `ATTR_EMULATE_PREPARES` is disabled. Passing integers through `execute()` directly causes silent failures in this configuration.

**Auto-routing** — `App.php` resolves URLs to controllers and methods by naming convention. `/nurse/health-records` maps to `NurseController::health_records()` automatically, with hyphen-to-underscore conversion and `FooController.php` suffix detection built in.

**Role inheritance** — `Nurse` and `Admin` extend `User`, sharing base query methods while overriding `$table` behavior as needed. Student queries in `Admin` use raw `$this->query()` explicitly since the inherited `$table` points to `users`.

---

## User Roles

**Admin** : Manage users, students, staff assignments, system overview
**Nurse** : Health records, medications, health events per assigned students
**Teacher** : Sessions, TEACCH progress, homework
**Therapist** : IEP goals, goal progress, therapy sessions
**Parent** : View child's academic and health records
**Boarding Staff** : Daily boarding logs, check-in/check-out
**Security Guard** : Entry/exit check-in logs

---

## Database

The schema includes 24 tables. Key ones:

| Table | Purpose |
|---|---|
| `users` | All staff, parents, and admins |
| `students` | Student profiles |
| `nurse_student` | Nurse ↔ Student assignments |
| `student_assignments` | Teacher/Therapist ↔ Student assignments (with `role_type`, `start_date`, `end_date`) |
| `health_records` | Medical notes, allergies, vaccinations |
| `medications` + `medication_logs` | Active medications and dose administration history |
| `health_events` | Incident reports with severity |
| `iep_goals` + `goal_progress` | Individualized Education Program tracking |
| `sessions` + `session_students` | Therapy and class sessions |
| `teacch_tasks` + `teacch_progress` | TEACCH structured task progress |

---

## Setup

1. Clone the repo and place it in your XAMPP `htdocs` folder
2. Import `mysess_db.sql` into MySQL
3. Update `app/core/config.php` with your DB credentials and `ROOT` path
4. Add a `.htaccess` in the project root:
   ```
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ public/index.php?url=$1 [L,QSA]
   ```
5. Visit `http://localhost/mysess_new`

---

## Authors

- **Saja Jaber** — [github.com/sajajaber](https://github.com/sajajaber)
- **Fatima Honeino** — [github.com/fatimahoneino](https://github.com/fatimahoneino)

Lebanese International University — Senior Project, 2025–2026
