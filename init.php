<?php
// Source: schema.sql

require_once 'helpers.php';
require 'db.php';

// Connect to the database with manualy select DB
$con = db_connect(true);

// Delete the old database before using it
db_drop($con, $db['name']);

// Create DB
$result = db_create($con, $db['name']);

if ($result) {
  // Select DB
  db_select($con);
}

// Create Tabels

// Errors
$create_table_errors = [];

// Create Users table
['error' => $create_table_errors['users']] = db_create_table(
  $con,
  "CREATE TABLE users ( " .
    "id INT AUTO_INCREMENT PRIMARY KEY, " .
    "email VARCHAR(128) NOT NULL UNIQUE, " .
    "password CHAR(64) NOT NULL, " .
    "name VARCHAR(64) NOT NULL, " .
    "contacts TEXT, " .
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
    ");"
);

// Create Categories table
['error' => $create_table_errors['categories']] = db_create_table(
  $con,
  "CREATE TABLE categories ( " .
    "id INT AUTO_INCREMENT PRIMARY KEY, " .
    "slug VARCHAR(128) NOT NULL UNIQUE, " .
    "title VARCHAR(128) NOT NULL, " .
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
    ");"
);

// Create Lots table
['error' => $create_table_errors['lots']] = db_create_table(
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
    "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
    ");"
);

// Create Bets table
['error' => $create_table_errors['bets']] = db_create_table(
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
echo '<p style="color: green;">Database «' . $db['name'] . '» was successfully created!</p>';

foreach ($create_table_errors as $key => $create_table_error) {
  if (is_array($create_table_error) && isset($create_table_error['message'])) {
    echo '<p style="color: red;">Database Table «' . $key . '» was not created due to an error: ' . $create_table_error['message'] . '</p>';
  } else {
    echo '<p style="color: green;">Database Table «' . $key . '» was successfully created!</p>';
  }
}
