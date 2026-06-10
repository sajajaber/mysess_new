

CREATE TABLE IF NOT EXISTS classroom_sessions (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    student_id   INT          NOT NULL,
    teacher_id   INT          NOT NULL,
    session_date DATE         NOT NULL,
    subject      VARCHAR(150) NOT NULL,
    notes        TEXT         NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cs_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_cs_teacher FOREIGN KEY (teacher_id) REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE IF NOT EXISTS academic_observations (
    id          INT       AUTO_INCREMENT PRIMARY KEY,
    student_id  INT       NOT NULL,
    teacher_id  INT       NOT NULL,
    session_id  INT       NULL,
    observation TEXT      NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ao_student FOREIGN KEY (student_id) REFERENCES students(id)           ON DELETE CASCADE,
    CONSTRAINT fk_ao_teacher FOREIGN KEY (teacher_id) REFERENCES users(id)              ON DELETE CASCADE,
    CONSTRAINT fk_ao_session FOREIGN KEY (session_id) REFERENCES classroom_sessions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS iep_goals (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    student_id       INT          NOT NULL,
    goal_description TEXT         NOT NULL,
    status           VARCHAR(20)  NOT NULL DEFAULT 'active',
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ig_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS iep_goal_progress (
    id              INT          AUTO_INCREMENT PRIMARY KEY,
    goal_id         INT          NOT NULL,
    teacher_id      INT          NOT NULL,
    progress_note   TEXT         NULL,
    progress_status VARCHAR(30)  NOT NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_igp_goal    FOREIGN KEY (goal_id)    REFERENCES iep_goals(id) ON DELETE CASCADE,
    CONSTRAINT fk_igp_teacher FOREIGN KEY (teacher_id) REFERENCES users(id)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS progress_reports (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    student_id       INT          NOT NULL,
    teacher_id       INT          NOT NULL,
    reporting_period VARCHAR(100) NOT NULL,
    summary          TEXT         NOT NULL,
    rating           VARCHAR(30)  NOT NULL,
    created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pr_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_pr_teacher FOREIGN KEY (teacher_id) REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
