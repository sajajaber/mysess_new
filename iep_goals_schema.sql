CREATE TABLE IF NOT EXISTS iep_goal_bank (
id          INT          AUTO_INCREMENT PRIMARY KEY,
category    VARCHAR(100) NOT NULL,                    -- one of the fixed categories
goal_text   TEXT         NOT NULL,                    -- the reusable goal wording
is_active   TINYINT(1)   NOT NULL DEFAULT 1,          -- 1 = shows in staff pickers, 0 = hidden
created_by  INT          NOT NULL,                    -- which admin created it (users.id)
created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
CONSTRAINT fk_goalbank_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) 

ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
