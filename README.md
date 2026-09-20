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

## Phase 4: Authentication (Hashing + Session Tracking)

| Feature | File(s) | What it does |
|---------|---------|---------------|
| Password hashing | `signup.php`, `change_password.php` | `password_hash()` scrambles the password before saving — plain passwords are never stored |
| Password verification | `login.php`, `change_password.php` | `password_verify()` checks a typed password against the stored hash |
| Centralized login check | `auth_check.php` | Every protected page (`dashboard.php`, `upload.php`, `wishlist.php`, `edit_book.php`, `delete_book.php`, `save_wishlist.php`, `remove_wishlist.php`) includes this one file instead of repeating the same check |
| Session tracking | `auth_check.php`, `login.php` | Tracks `last_activity` time; auto-logs out after 30 minutes of inactivity |
| Session fixation protection | `login.php` | `session_regenerate_id(true)` on every successful login |
| Change Password module | `change_password.php` | Lets a logged-in user verify their old password and set a new (re-hashed) one |

## Admin Panel + Hover-to-Play Videos

### Admin System (separate login, separate table)

Admins are stored in their own `admins` table — completely separate from
the `users` table, so an admin login and a regular user login never mix up.

**First-time setup:**
1. Open `http://localhost/storyspill/admin_signup.php` once, create your admin username/password.
2. Delete or rename `admin_signup.php` afterwards (it's only meant to be used once).
3. From then on, login at `http://localhost/storyspill/admin_login.php`.

| File | What it does |
|------|----------------|
| `admin_signup.php` | One-time page to create the first admin account (hashed password) |
| `admin_login.php` | Admin login (separate from user login), verifies with `password_verify()` |
| `admin_auth_check.php` | Central admin session guard (like `auth_check.php`, but for admins) |
| `admin_dashboard.php` | Shows: users currently online, total signups, total books uploaded, and a recent users table with online/offline status |
| `admin_logout.php` | Destroys the admin session |

**How "currently online" is calculated:** every time a logged-in user visits
a page (`auth_check.php` or `index.php`), their `last_activity` timestamp is
updated in the `users` table. The admin dashboard counts anyone active in
the last 5 minutes as "online."

### Hover-to-Play Video on Book Covers

On the home page, each book card now shows its cover image by default.
When you hover your mouse over the card, the cover fades out and the
book's intro video fades in and starts playing (muted, looped). Moving
the mouse away pauses the video and shows the cover again.

### Home Page: Instagram Explore-style Grid

The home page now looks like Instagram's Explore/Search grid: square
thumbnails packed edge-to-edge with tiny gaps, no title/author text
showing by default. When you hover a card, a dark overlay fades in
showing the title, author, uploader, and the Save/Read buttons — and
the cover image fades out while the intro video fades in and plays.

- CSS (`style.css`) handles the square grid (`.book-grid`), the hover fade
  (`.cover-img`, `.hover-video`), and the overlay (`.card-overlay`).
- JavaScript (`script.js`) handles actually starting/stopping video playback,
  since CSS alone cannot call `.play()` on a `<video>` element.
- If a book has no intro video, it just shows the cover image as before — nothing breaks.
