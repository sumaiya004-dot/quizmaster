Technical Documentation: QuizMaster
1. Technologies Used
Frontend: HTML, CSS, JavaScript, Bootstrap
Backend: PHP
Database: MySQL

2. System Architecture
The system follows a structured MVC-like pattern:
Models: Handle database operations
Views: Display user interface
Controllers: Process logic and user requests

3. Database Schema
Table: users
id (Primary Key)
name
email
password (hashed)
Table: quizzes
id
subject
difficulty
Table: questions
id
quiz_id (Foreign Key)
question
option_a
option_b
option_c
option_d
correct_answer
Table: results
id
user_id (Foreign Key)
score
date

4. Relationship (ER Concept)
One user → many results
One quiz → many questions
(ER diagram is attached separately as an image)

5. Security Implementation
Password hashing using PHP password_hash()
Prepared statements to prevent SQL injection
Session-based authentication system

6. Setup Instructions
Install XAMPP
Copy project folder into htdocs
Open phpMyAdmin
Create a database named: quizmaster
Import sql/schema.sql file
Configure database connection in config/database.php
Run in browser:
http://localhost/quizmaster

7. Features Implementation
CRUD operations implemented for quiz and results
Authentication and authorization system
Error handling for invalid input
Responsive design using Bootstrap

