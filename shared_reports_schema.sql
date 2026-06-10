CREATE TABLE IF NOT EXISTS `shared_reports` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `shared_by`  INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shared_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
