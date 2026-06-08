
CREATE TABLE IF NOT EXISTS therapy_sessions (
    id                INT          AUTO_INCREMENT PRIMARY KEY,
    student_id        INT          NOT NULL,                       
    therapist_id      INT          NOT NULL,                      
    session_date      DATE         NOT NULL,                       
    session_type      VARCHAR(100) NOT NULL,                     
    status            VARCHAR(20)  NOT NULL DEFAULT 'scheduled',   
    performance_notes TEXT         NULL,                           
    goal_addressed_id INT          NULL,                           
    created_at        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tsess_student   FOREIGN KEY (student_id)        REFERENCES students(id)  ON DELETE CASCADE,
    CONSTRAINT fk_tsess_therapist FOREIGN KEY (therapist_id)      REFERENCES users(id)     ON DELETE CASCADE,
    CONSTRAINT fk_tsess_goal      FOREIGN KEY (goal_addressed_id) REFERENCES iep_goals(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
