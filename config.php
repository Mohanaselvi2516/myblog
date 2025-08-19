<?php
// config.php
$host = "127.0.0.1";
$db   = "apex_blog";  // 👈 use your database name
$user = "root";       // default XAMPP username
$pass = "";           // default XAMPP password (empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}