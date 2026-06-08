
SET @admin := (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1);

INSERT INTO iep_goal_bank (category, goal_text, is_active, created_by) VALUES

-- Communication
('Communication', 'The student will request a desired item or activity using words, signs, or a picture symbol in 4 out of 5 opportunities across 3 consecutive sessions.', 1, @admin),
('Communication', 'The student will answer simple "who", "what", and "where" questions with at least 80% accuracy over 3 consecutive sessions.', 1, @admin),
('Communication', 'The student will follow a one-step verbal direction without a prompt in 8 out of 10 opportunities.', 1, @admin),
('Communication', 'The student will greet a familiar person using words or a sign when prompted in 4 out of 5 opportunities.', 1, @admin),
('Communication', 'The student will use a 2 to 3 word phrase to comment on an activity at least 5 times during a 30 minute session.', 1, @admin),
('Communication', 'The student will gain a communication partner''s attention appropriately (for example, saying the person''s name) before making a request in 8 out of 10 opportunities.', 1, @admin),

-- Social
('Social', 'The student will take turns during a structured game with a peer for at least 4 turns with no more than one prompt.', 1, @admin),
('Social', 'The student will share materials with a peer when asked in 4 out of 5 opportunities across 3 sessions.', 1, @admin),
('Social', 'The student will maintain appropriate personal space during group activities in 8 out of 10 observed opportunities.', 1, @admin),
('Social', 'The student will initiate a greeting or simple interaction with a peer at least 2 times per session.', 1, @admin),
('Social', 'The student will identify a basic emotion (happy, sad, angry, scared) in self or others with 80% accuracy.', 1, @admin),
('Social', 'The student will join an ongoing peer activity using an appropriate phrase in 3 out of 4 opportunities.', 1, @admin),

-- Motor
('Motor', 'The student will use a pincer grasp to pick up small objects in 4 out of 5 trials.', 1, @admin),
('Motor', 'The student will cut along a straight line within 1/4 inch of the line in 4 out of 5 trials.', 1, @admin),
('Motor', 'The student will copy basic shapes (circle, square, triangle) with 80% accuracy.', 1, @admin),
('Motor', 'The student will catch a ball thrown from 5 feet away in 7 out of 10 trials.', 1, @admin),
('Motor', 'The student will write their first name legibly with correct letter formation in 4 out of 5 trials.', 1, @admin),
('Motor', 'The student will walk up and down a set of stairs using alternating feet with no more than one prompt.', 1, @admin),

-- Academic
('Academic', 'The student will identify the letters of the alphabet with 80% accuracy across 3 consecutive sessions.', 1, @admin),
('Academic', 'The student will count objects up to 20 with one-to-one correspondence with 90% accuracy.', 1, @admin),
('Academic', 'The student will read a list of grade-level sight words with at least 80% accuracy.', 1, @admin),
('Academic', 'The student will solve single-digit addition problems with 80% accuracy over 3 sessions.', 1, @admin),
('Academic', 'The student will answer simple comprehension questions after a short story with 75% accuracy.', 1, @admin),
('Academic', 'The student will sort objects by color, shape, or size with 90% accuracy.', 1, @admin),

-- Behavioral
('Behavioral', 'The student will remain seated and engaged during a 10 minute structured task with no more than two reminders.', 1, @admin),
('Behavioral', 'The student will use a calming strategy (for example, deep breaths) when frustrated in 4 out of 5 opportunities.', 1, @admin),
('Behavioral', 'The student will transition between activities within 1 minute of the request in 8 out of 10 opportunities.', 1, @admin),
('Behavioral', 'The student will raise their hand and wait to be called on before speaking in 4 out of 5 opportunities.', 1, @admin),
('Behavioral', 'The student will follow classroom rules during a full activity with no more than one redirection.', 1, @admin),
('Behavioral', 'The student will accept a change in routine without disruptive behavior in 3 out of 4 opportunities.', 1, @admin),

-- Daily Living
('Daily Living', 'The student will wash and dry their hands following all steps independently in 4 out of 5 opportunities.', 1, @admin),
('Daily Living', 'The student will put on and fasten their coat independently in 4 out of 5 opportunities.', 1, @admin),
('Daily Living', 'The student will use utensils to eat a meal with minimal spilling in 4 out of 5 opportunities.', 1, @admin),
('Daily Living', 'The student will clean up their workspace after an activity with no more than one prompt.', 1, @admin),
('Daily Living', 'The student will follow a visual schedule to complete a morning routine with 80% independence.', 1, @admin),
('Daily Living', 'The student will pack their bag with the correct items before leaving in 4 out of 5 opportunities.', 1, @admin);
