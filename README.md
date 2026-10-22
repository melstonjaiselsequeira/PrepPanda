# PrepPanda — local setup (without XAMPP)

This project uses PHP's built-in development server and a separately installed MySQL Server. XAMPP is not required.

## Requirements
- PHP 8.2+ for Windows with the `mysqli` extension enabled
- MySQL Server 8+
- VS Code (optional but recommended)

## 1. Configure MySQL
1. Start the **MySQL80** service from Windows Services, or start your installed MySQL Server using its configured service name.
2. Open MySQL Workbench and connect to your local server.
3. Open `database/preppanda.sql` and execute the entire script. This creates the `preppanda` database and its tables.

## 2. Configure the database password
Open `config.php`. By default it uses `127.0.0.1`, username `root`, an empty password, and database `preppanda`.
If you created a MySQL root password, either set `PREPPANDA_DB_PASS` in your terminal environment or update the local `db_pass` value in `config.php` for development. Never commit production credentials to source control. For production, use environment variables and set `PREPPANDA_ENV=production`.

Environment variables supported:
- `PREPPANDA_DB_HOST`
- `PREPPANDA_DB_USER`
- `PREPPANDA_DB_PASS`
- `PREPPANDA_DB_NAME`
- `PREPPANDA_ENV` (`local` or `production`)

## 3. Verify PHP
Open a VS Code terminal at the project root (the folder containing `index.php`) and run:

```powershell
php -v
php -m | findstr mysqli
```

If `php` is not on PATH, use the full path to your PHP executable. If `mysqli` does not appear, enable `extension=mysqli` in the `php.ini` used by that PHP installation, then restart the terminal.

## 4. Start the application
From the project root:

```powershell
php -S 127.0.0.1:8000
```

Then open http://127.0.0.1:8000/ . Keep the terminal open while using the app. Stop it with Ctrl+C.

## 5. First run
- Open Sign up and create a teacher account. A teacher group is created automatically.
- Create a student account in another browser profile or after logging out, then use the group code shown on the teacher dashboard to join the group.
- The built-in PHP server is for local development only, not a production deployment server.

## Notes and limitations
- `database/preppanda.sql` creates the schema required by the PHP pages in this package; it does not contain real user data.
- This ZIP originally omitted `includes/`, `assets/`, the SQL schema, and an upload directory. Those missing pieces are supplied here.
- Before public deployment, perform a security review of every role-specific action and configure HTTPS, secure cookies, CSRF protection, upload scanning/limits, backups, logging, and a production web server. The built-in PHP server is not suitable for public production traffic.
