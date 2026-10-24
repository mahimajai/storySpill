StorySpill
Interactive Digital Bookshelf Library
StorySpill is a full-stack web application that presents user-uploaded books as a visual, neon-colored virtual bookshelf. Inspired by the “Interesting Reads” style of digital libraries, it combines a rich front-end experience with a complete user and admin backend.
---
Project Overview
Item	Detail
Project Name	StorySpill
Type	Web Application (Library / Content Sharing)
Front-end	HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
Back-end	PHP 8 (PDO)
Database	MySQL
Server	Apache (XAMPP )
---
Features
User Features
Sign up / Login / Logout (secure password hashing)
Strong password policy (8+ chars, upper, lower, number, special character)
Upload Book (title, author, genre, cover, intro video, PDF)
Interactive neon bookshelf with abstract spine patterns
Genre filter — non-matching books animate and fall off the shelf
Search by title, author, or genre
“Surprise Me” random book picker
Book detail modal with cover hover → video plays with sound
Wishlist (“Shelve It”)
My Profile — uploaded books, edit/delete own books, change password
Admin Features
Separate admin login / signup (company code: `Mahi052005`)
Strong password policy on admin signup
Dashboard — total users, online users, total books
Click any user → full profile (books + wishlist)
Edit / delete any book
Delete any user (cascades their books and wishlist)
Full control over the platform
---
Password Policy (Google-style)
When creating or changing a password, the system requires:
At least 8 characters
At least 1 uppercase letter (A–Z)
At least 1 lowercase letter (a–z)
At least 1 number (0–9)
At least 1 special character (`!@#$%^&*` etc.)
Applied on: Sign Up, Admin Sign Up, Change Password.
> Demo seeded accounts still use simple passwords for easy testing. New accounts must follow the policy.
---
Prerequisites
XAMPP (Apache + MySQL + PHP 8.x with PDO)
Modern browser (Chrome / Edge / Firefox)
---
Project Structure
```
storyspill03/
├── index.php, login.php, signup.php, logout.php
├── upload.php, dashboard.php, wishlist.php, change_password.php
├── edit_book.php, delete_book.php
├── admin_login.php, admin_signup.php, admin_dashboard.php
├── admin_user.php, admin_edit_book.php
├── admin_delete_book.php, admin_delete_user.php, admin_logout.php
├── db_connect.php, schema.sql, seed.sql
├── css/style.css, js/app.js
├── includes/ (navbar, auth_check, admin_auth_check, password_rules)
└── uploads/ (covers, videos, pdfs)
```
---
Database Setup
Start Apache + MySQL in XAMPP.
phpMyAdmin → create database `storyspill_db1` (utf8mb4).
Import schema.sql then seed.sql.
Tables
`users` · `admins` · `books` · `wishlist`
---
Configuration
`db_connect.php`:
```php
$host     = "localhost";
$dbname   = "storyspill_db1";
$username = "root";
$password = "";   // or your MySQL password
```
Folder location:
```
C:\xampp\htdocs\storyspill03\
```
URL: `http://localhost/storyspill03/`
---
Default Accounts (seed data)
Users (password = name — demo only)
Email	Password
diya@gmail.com	diya
neha@gmail.com	neha
… (12 users total)	same as name
Admin
Field	Value
Username	`mahima`
Password	`mahi`
Company code (new admin)	`Mahi052005`
---
Security
`password_hash` / `password_verify` (bcrypt)
PDO prepared statements
Strong password validation on registration
Separate admin table + company code gate
Session-based role checks
---
License / Academic Use
For academic demonstration and portfolio use. Sample media is illustrative only.
