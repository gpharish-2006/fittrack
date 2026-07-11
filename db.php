<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "fitness_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $password, $dbname);
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    die("Database Connection Failure: " . htmlspecialchars($e->getMessage()));
}
?>