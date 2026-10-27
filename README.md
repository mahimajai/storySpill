# StorySpill

**Interactive Digital Bookshelf Library**

Full-stack web app: neon virtual bookshelf, multi-user uploads, wishlist, **reviews**, **reports**, and admin control.

|Item|Detail|
|-|-|
|Front-end|HTML5, CSS3, Bootstrap 5, Vanilla JavaScript|
|Back-end|PHP 8 (PDO, Sessions)|
|Database|MySQL — **7 tables**|
|Server|Apache (XAMPP)|

\---

## Database Tables (7)

|Table|Purpose|
|-|-|
|`users`|Registered readers / uploaders|
|`admins`|Privileged accounts (company-code signup)|
|`genres`|Master list of 20 genres|
|`books`|Title, author, genre, cover, video, PDF|
|`wishlist`|User ↔ book saves|
|`reviews`|Ratings (1–5) + optional review text|
|`reports`|Moderation queue (`pending` / `reviewed` / `resolved`)|

\---

## Features

### User

* Signup / Login with **strong password policy** (8+, upper, lower, digit, special)
* Upload book (cover + intro video + PDF); genres from DB
* Neon shelf, genre fall animation, search, Surprise Me
* Cover hover → video with sound
* Wishlist (Shelve It)
* **Review button** on book modal → saves to `reviews`
* **Report button** on book modal → saves to `reports`

### Admin

* Login / signup (company code: `Mahi052005`)
* Dashboard: users, books, genres, reviews, pending reports counts
* Click user → profile, edit/delete books, delete user
* **admin\_reports.php** — mark reviewed / resolve

\---

## How Review \& Report work (UI → DB)

1. User logs in → opens a book modal
2. Selects stars + optional text → **Review** → `save\_review.php` → `reviews` table
3. Clicks **Report** → enters reason → `save\_report.php` → `reports` table (`pending`)
4. Admin opens `admin\_reports.php` → Mark reviewed / Resolve

APIs: `save\_review.php`, `get\_book\_meta.php`, `save\_report.php`

\---

## Setup

1. Place project in `htdocs/storyspill65` (or your folder name)
2. Create folders: `uploads/covers`, `uploads/videos`, `uploads/pdfs`
3. phpMyAdmin: import **schema.sql** then **seed.sql**
4. `db\_connect.php`: set `$dbname`, `$password`
5. Open `http://localhost/storyspill65/`

### Default accounts

* User: `diya@gmail.com` / `diya`
* Admin: `mahima` / `mahi`
* Company code: `Mahi052005`

### Seed checks

```sql
SELECT COUNT(\*) FROM genres;   -- 20
SELECT COUNT(\*) FROM reviews;  -- 12
SELECT COUNT(\*) FROM reports;  -- 4
```

\---

## Key files

|File|Role|
|-|-|
|`index.php`|Shelf + modal (Review / Report UI)|
|`js/app.js`|Filter, modal, review/report JS|
|`save\_review.php` / `get\_book\_meta.php`|Reviews API|
|`save\_report.php`|Report API|
|`admin\_reports.php`|Admin moderation|
|`schema.sql` / `seed.sql`|7 tables + sample data|

\---

## License

Academic / portfolio demonstration.

