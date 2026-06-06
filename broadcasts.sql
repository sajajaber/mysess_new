CREATE TABLE `broadcasts` (
  `id`           int(11)      NOT NULL AUTO_INCREMENT,
  `sender_id`    int(11)      NOT NULL,
  `target_roles` varchar(255) NOT NULL,
  `subject`      varchar(255) NOT NULL,
  `body`         text         NOT NULL,
  `created_at`   timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);