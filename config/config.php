<?php

$baseUrl = 'http://localhost/pupuk2';

// Database Connection
$dbHost = 'localhost';
$dbName = 'pupuk-main';
$dbUser = 'root';
$dbPassword = '';

function databaseConnection()
{
    global $dbHost, $dbName, $dbUser, $dbPassword;

    try {
        // Create a new PDO instance
        $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPassword);

        // Set PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Additional database configuration (optional)
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Return the PDO object
        return $db;
    } catch (PDOException $e) {
        // Handle database connection errors
        echo "Failed to connect to the database: " . $e->getMessage();
        die();
    }
}

function checkUrl($url)
{
    echo strpos($_SERVER['REQUEST_URI'], $url) !== false ? 'active' : '';
}
