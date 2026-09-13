# StorySpill - Setup Guide (Phase 3: PHP + MySQL with PDO)

## Requirements
- XAMPP / WAMP / MAMP (or any server with PHP 7+ and MySQL)

## Steps to Run

1. Copy the whole `storyspill` folder into your server's web root:
   - XAMPP: `htdocs/storyspill`
   - WAMP: `www/storyspill`

2. Start Apache and MySQL from your XAMPP/WAMP control panel.

3. Create the database:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Click "Import" and choose the file `schema.sql`
   - This creates the `storyspill_db` database with `users`, `books`, and `wishlist` tables.

4. Check `db_connect.php` — by default it uses:
   ```
   host: localhost
   dbname: storyspill_db
   username: root
   password: (empty)
   ```
   Update these if your MySQL setup is different.

5. Make sure the `uploads/covers` and `uploads/videos` folders are writable
   (they already exist in this project — they store uploaded cover images and videos).

6. Open the app in your browser:
   ```
   http://localhost/storyspill/
   ```
   This opens `index.php` (Home page). Click "Sign Up" to create an account.

## CRUD Operations Implemented (PDO + MySQL)

| Operation | File(s)                          | What it does                                  |
|-----------|-----------------------------------|------------------------------------------------|
| Create    | `signup.php`                      | Insert new user (password hashed)              |
| Create    | `upload.php`                      | Insert new book with cover/video files         |
| Create    | `save_wishlist.php`               | Insert a book into the wishlist                |
| Read      | `login.php`                       | Select user, verify password, start session    |
| Read      | `index.php`                       | Select all books (with search by title/author) |
| Read      | `dashboard.php`                   | Select logged-in user's profile and books      |
| Read      | `wishlist.php`                    | Select wishlist joined with books              |
| Update    | `edit_book.php`                   | Update a book's title/author/genre             |
| Delete    | `delete_book.php`                 | Delete a book (and its uploaded files)         |
| Delete    | `remove_wishlist.php`             | Remove a book from the wishlist                |

All database access uses **PDO with prepared statements** (`db_connect.php`),
which protects against SQL injection.
