-- Add question type and explanation support in questions table.
ALTER TABLE questions
    ADD COLUMN question_type ENUM('mcq', 'tf', 'short') NOT NULL DEFAULT 'mcq' AFTER question_text,
    ADD COLUMN explanation TEXT NULL AFTER question_type;
