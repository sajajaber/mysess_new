ALTER TABLE students
  ADD COLUMN is_boarding TINYINT(1) NOT NULL DEFAULT 0 AFTER diagnosis;

ALTER TABLE student_assignments
  MODIFY COLUMN role_type ENUM('teacher','therapist','boarding_staff') NOT NULL;
