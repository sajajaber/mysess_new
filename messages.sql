CREATE TABLE `messages` (
  `id`          int(11)      NOT NULL AUTO_INCREMENT,
  `sender_id`   int(11)      NOT NULL,
  `receiver_id` int(11)      NOT NULL,
  `subject`     varchar(255) NOT NULL,
  `body`        text         NOT NULL,
  `is_read`     tinyint(1)   NOT NULL DEFAULT 0,
  `created_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`sender_id`)   REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);