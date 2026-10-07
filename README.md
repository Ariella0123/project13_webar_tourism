# AR Tourism Explorer

Generic PHP 8.3/MySQL 8 tourism platform with public destination pages, secure administrator login, AR poster data, normalized hotspots and a mobile AR entry page.

## Install on XAMPP

1. Copy this folder into `C:\xampp\htdocs\project13_webar_tourism`.
2. Start Apache and MySQL.
3. Open phpMyAdmin and import `database/database.sql`.
4. Check credentials in `includes/config.php`.
5. Open `http://localhost/project13_webar_tourism/`.
6. Admin login: `admin@example.com` / `password`. Change this password immediately in production.

## AR target compilation

MindAR target compilation is not safely performed by PHP alone. Use the official MindAR Image Targets Compiler to create a `.mind` file from each poster, copy it into `assets/ar-targets/`, then set `ar_posters.target_file` and `target_status=READY`. A production deployment should restrict target files to non-executable content and serve the site over HTTPS.

The API endpoint `api/ar/poster.php` returns poster metadata and hotspot coordinates. Hotspots use normalized `x`, `y`, `width`, and `height` values so the frontend can map them to any poster size.

## Security

PDO prepared statements, password hashing, CSRF tokens, session regeneration, output escaping and MIME/dimension validated image uploads are included. Configure HTTPS, replace the demo password, disable directory listing and ensure upload directories cannot execute PHP.
