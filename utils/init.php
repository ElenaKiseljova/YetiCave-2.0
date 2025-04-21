<?php
// Source: schema.sql

require_once $_SERVER['DOCUMENT_ROOT'] . '/env/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

// Connect to the database with manualy select DB
$db = new DBController();

$con = $db->connect(true);

// Delete the old database before using it
$db->drop($con, $dbParameters['name']);

// Create DB
$result = $db->create($con, $dbParameters['name']);

if ($result) {
  // Select DB
  $db->select($con);
}

// Create Tabels

// Errors
$createTableErrors = [];

// Create Users table
['error' => $createTableErrors['users']] = $db->createTable(
  $con,
  "CREATE TABLE users ( " .
    "id INT AUTO_INCREMENT PRIMARY KEY, " .
    "email VARCHAR(128) NOT NULL UNIQUE, " .
    "password VARCHAR(255) NOT NULL, " .
    "name VARCHAR(64) NOT NULL UNIQUE, " .
    "contacts TEXT, " .
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
    ");"
);

// Create Categories table
['error' => $createTableErrors['categories']] = $db->createTable(
  $con,
  "CREATE TABLE categories ( " .
    "id INT AUTO_INCREMENT PRIMARY KEY, " .
    "slug VARCHAR(128) NOT NULL UNIQUE, " .
    "title VARCHAR(128) NOT NULL, " .
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
    ");"
);

// Create Lots table
['error' => $createTableErrors['lots']] = $db->createTable(
  $con,
  "CREATE TABLE lots ( " .
    "id INT AUTO_INCREMENT PRIMARY KEY, " .
    "slug VARCHAR(64) NOT NULL UNIQUE, " .
    "title VARCHAR(128) NOT NULL, " .
    "image VARCHAR(128) NOT NULL, " .
    "description TEXT, " .
    "price_start INT NOT NULL, " .
    "price_step INT NOT NULL, " .
    "expiration_date TIMESTAMP DEFAULT NULL, " .
    "category_id INT DEFAULT NULL, " .
    "user_id INT NOT NULL, " .
    "winner_id INT DEFAULT NULL, " .
    "FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL, " .
    "FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE, " .
    "FOREIGN KEY (winner_id) REFERENCES users (id) ON DELETE SET NULL, " .
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, " .
    "FULLTEXT (title, description)" .
    ") ENGINE=InnoDB;"
);

// Create Bets table
['error' => $createTableErrors['bets']] = $db->createTable(
  $con,
  "CREATE TABLE bets ( " .
    "id INT AUTO_INCREMENT PRIMARY KEY, " .
    "price INT NOT NULL, " .
    "user_id INT NOT NULL, " .
    "lot_id INT DEFAULT NULL, " .
    "FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE, " .
    "FOREIGN KEY (lot_id) REFERENCES lots (id) ON DELETE CASCADE, " .
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
    ");"
);

echo '<h2>Create</h2>';
echo '<p style="color: green;">Database «' . $dbParameters['name'] . '» was successfully created!</p>';

foreach ($createTableErrors as $key => $createTableError) {
  if (is_array($createTableError) && isset($createTableError['message'])) {
    echo '<p style="color: red;">Database Table «' . $key . '» was not created due to an error: ' . $createTableError['message'] . '</p>';
  } else {
    echo '<p style="color: green;">Database Table «' . $key . '» was successfully created!</p>';
  }
}
