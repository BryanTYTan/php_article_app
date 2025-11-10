<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'app_database');
define('DB_USER', 'postgres');
define('DB_PASS', 'abc');

$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'abc';

$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

try {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    // SQL to insert the user data with the GENERATED hash
    $sql = "INSERT INTO article_app.users (username, email, password_hash, is_administrator)
            VALUES (:username, :email, :password_hash, TRUE)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':username' => $admin_username,
        ':email' => $admin_email,
        ':password_hash' => $hashed_password
    ]);

    echo "Admin user '{$admin_username}' securely inserted.\n";

} catch (PDOException $e) {
    die("Database operation failed: " . $e->getMessage() . "\n");
}