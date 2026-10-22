<?php
// Central MySQL connection used by all PrepPanda pages.
require_once __DIR__ . '/../config.php';
if (!extension_loaded('mysqli')) {
    http_response_code(500);
    exit('PrepPanda setup issue: PHP MySQLi extension is not enabled. Enable extension=mysqli in the php.ini used by your PHP server, then restart the server.');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    error_log('PrepPanda database connection failed: ' . $e->getMessage());
    exit('PrepPanda could not connect to MySQL. Check config.php and import database/preppanda.sql in MySQL Workbench.');
}
