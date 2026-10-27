# StorySpill

Interactive digital bookshelf (PHP + MySQL + Bootstrap).

## 7 Database Tables
users · admins · genres · books · wishlist · reviews · reports

## Setup
1. Copy folder to `htdocs/storyspill03`
2. Create DB / import **schema.sql** then **seed.sql**
3. Edit `db_connect.php` (dbname, user, password)
4. Open `http://localhost/storyspill03/`

## Defaults
- Admin: mahima / mahi · Company code: Mahi052005
- Users: diya@gmail.com / diya (password = name for seeded users)

## New APIs
- `save_review.php` / `get_book_meta.php` — ratings & reviews
- `save_report.php` — report a book
- `admin_reports.php` — moderation queue
