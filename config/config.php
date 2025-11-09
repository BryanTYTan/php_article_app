<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'your_database');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

function getDBConnection()
{
    try {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Set search path to article_app schema
        $pdo->exec("SET search_path TO article_app");
        
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['is_administrator']) && $_SESSION['is_administrator'] === true;
}

function redirect($page)
{
    // Map page names to actual paths
    $routes = [
        'login.php' => '/src/auth/login.php',
        'register.php' => '/src/auth/register.php',
        'home.php' => '/src/main/home.php',
        'logout.php' => '/src/auth/logout.php'
    ];

    $path = isset($routes[$page]) ? $routes[$page] : $page;
    header("Location: $path");
    exit();
}
?>