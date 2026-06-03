
SET @admin := (SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1);

INSERT INTO teacch_task_bank (category, title, is_active, created_by) VALUES

-- Self-Care
('Self-Care', 'Wash hands', 1, @admin),
('Self-Care', 'Brush teeth', 1, @admin),
('Self-Care', 'Comb hair', 1, @admin),
('Self-Care', 'Put on shoes', 1, @admin),
('Self-Care', 'Zip up jacket', 1, @admin),
('Self-Care', 'Blow nose and throw tissue away', 1, @admin),

-- Daily Living
('Daily Living', 'Set the table', 1, @admin),
('Daily Living', 'Clear plate after eating', 1, @admin),
('Daily Living', 'Pour a drink', 1, @admin),
('Daily Living', 'Make a simple sandwich', 1, @admin),
('Daily Living', 'Put dirty clothes in the basket', 1, @admin),
('Daily Living', 'Wipe the table', 1, @admin),

-- Classroom Routine
('Classroom Routine', 'Hang up backpack', 1, @admin),
('Classroom Routine', 'Take out morning work', 1, @admin),
('Classroom Routine', 'Move name to the attendance chart', 1, @admin),
('Classroom Routine', 'Line up at the door', 1, @admin),
('Classroom Routine', 'Sharpen a pencil', 1, @admin),
('Classroom Routine', 'Return materials to the shelf', 1, @admin),

-- Play/Leisure
('Play/Leisure', 'Choose an activity from the choice board', 1, @admin),
('Play/Leisure', 'Take turns in a simple game', 1, @admin),
('Play/Leisure', 'Build with blocks for five minutes', 1, @admin),
('Play/Leisure', 'Complete a puzzle', 1, @admin),
('Play/Leisure', 'Put away toys when finished', 1, @admin),
('Play/Leisure', 'Listen to a story at the listening center', 1, @admin),

-- Vocational
('Vocational', 'Sort items by color', 1, @admin),
('Vocational', 'Match objects to pictures', 1, @admin),
('Vocational', 'Stuff envelopes', 1, @admin),
('Vocational', 'Stack cups in groups of five', 1, @admin),
('Vocational', 'Water the classroom plants', 1, @admin),
('Vocational', 'Deliver a note to the office', 1, @admin),

-- Communication
('Communication', 'Use a picture card to ask for a break', 1, @admin),
('Communication', 'Greet a peer using words or a sign', 1, @admin),
('Communication', 'Point to a choice when offered two options', 1, @admin),
('Communication', 'Answer a yes/no question', 1, @admin),
('Communication', 'Ask for help using a sentence strip', 1, @admin),
('Communication', 'Follow a one-step picture direction', 1, @admin);
