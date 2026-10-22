PrepPanda - local setup (PHP + MySQL, no XAMPP required)

1) Install MySQL Server and start the MySQL service.
2) Open MySQL Workbench, connect to your local server, open database/preppanda.sql and execute it.
3) Edit config.php and set DB_PASS to your MySQL root password (DB_USER defaults to root).
4) Enable the MySQLi PHP extension in the php.ini used by your PHP installation:
   extension=mysqli
   extension_dir="ext"
   Save php.ini and restart the PHP server.
5) Open a terminal in this folder and run:
   php -c "FULL_PATH_TO_YOUR_PHP_FOLDER/php.ini" -S 127.0.0.1:8000
   Replace FULL_PATH_TO_YOUR_PHP_FOLDER with your actual PHP folder. If php.ini is loaded by default, run: php -S 127.0.0.1:8000
6) Visit http://127.0.0.1:8000/index.php

Important: PHP and MySQL are separate programs. This project archive cannot enable PHP extensions on your computer automatically. If MySQLi is unavailable, PHP must be configured locally as above.

Uploads are stored in uploads/notes and uploads/tasks. Ensure PHP has write permission to these folders.
