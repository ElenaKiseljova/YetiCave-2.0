<?php
// Source: queries.sql

require_once 'helpers.php';
// when installed via composer
require 'vendor/autoload.php';

// Connect with database
$con = db_connect();

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

// Seeds Tabels

$USERS_COUNT = 5;
$LOTS_COUNT = 25;
$BETS_COUNT = 4;

// Logs
$logs = [];

// Categories
try {
  mysqli_query(
    $con,
    "INSERT INTO " .
      "categories (slug, title) " .
      "VALUES " .
      "('boards', 'Доски и лыжи'), " .
      "('attachment', 'Крепления'), " .
      "('boots', 'Ботинки'), " .
      "('clothing', 'Одежда'), " .
      "('tools', 'Инструменты'), " .
      "('other', 'Разное') "
  );

  $logs['categories']['success'] = true;
} catch (\Throwable $th) {
  $logs['categories']['error'] = mysqli_error($con);
}

// Users
try {
  // Users Values Array
  $sql_values_users = [];
  foreach (range(1, $USERS_COUNT) as $key) {
    $cols = [
      'name' => addslashes($faker->name()),
      'email' => $faker->email(),
      'password' => 'password',
      'address' => addslashes($faker->address()),
    ];

    foreach ($cols as $key => $col) {
      if (!is_int($col)) {
        $cols[$key] = "'" . $col . "'";
      }
    }

    $sql_values_users[] = "(" . implode(', ', $cols)  . ")";
  }

  // Users Values to String
  $sql_values_users = implode(', ', $sql_values_users);

  mysqli_query(
    $con,
    "INSERT INTO " .
      "users (name, email, password, contacts) " .
      "VALUES " .
      "$sql_values_users"
  );

  $logs['users']['success'] = true;

  // Lots
  try {
    // Lots Values Array
    $sql_values_lots = [];

    // Bets Values Array
    $sql_values_bets = [];

    foreach (range(1, $LOTS_COUNT) as $lot_id) {
      $user_id = mt_rand(1, $USERS_COUNT);
      $winner_id = mt_rand(0, 1) ? $faker->numberBetween(2, $USERS_COUNT) : "NULL";
      $winner_id = $winner_id === $user_id ? $winner_id - 1 : $winner_id;

      $cols = [
        'slug' => $faker->slug(2),
        'title' => addslashes($faker->sentence(mt_rand(3, 6))),
        'image' => "img/lot-" . mt_rand(1, 6) . ".jpg",
        'description' => addslashes($faker->paragraph()),
        'price_start' => mt_rand(1000, 1000000),
        'price_step' => mt_rand(100, 1000),
        'expiration_date' => $faker->dateTimeBetween('-10 days', '+10 days')->format('Y-m-d H:i:s'),
        'category_id' => mt_rand(1, 6),
        'user_id' => $user_id,
        'winner_id' => $winner_id,
      ];

      // Bets seed
      $price = $cols['price_start'];

      if ($lot_id % 3) {
        foreach (range(1, $BETS_COUNT) as $key) {
          $price = $cols['price_start'] + mt_rand(1, 10) * $cols['price_step'];
          $bet_user_id = 0;

          if ($key === $BETS_COUNT && is_int($winner_id)) {
            $bet_user_id = $winner_id;
          } else {
            $bet_user_id = $faker->numberBetween(3, $USERS_COUNT);
            $bet_user_id = $winner_id === $bet_user_id ? $bet_user_id - 1 : $bet_user_id;
          }

          $sql_values_bets[] = "(" . implode(', ', [$price, $bet_user_id, $lot_id])  . ")";
        }
      }

      foreach ($cols as $key => $col) {
        if (!is_int($col) && $col !== 'NULL') {
          $cols[$key] = "'" . $col . "'";
        }
      }

      $sql_values_lots[] = "(" . implode(', ', $cols)  . ")";
    }

    // Lots Values to String
    $sql_values_lots = implode(', ', $sql_values_lots);

    mysqli_query(
      $con,
      "INSERT INTO " .
        "lots ( " .
        "slug, " .
        "title, " .
        "image, " .
        "description, " .
        "price_start, " .
        "price_step, " .
        "expiration_date, " .
        "category_id, " .
        "user_id, " .
        "winner_id " .
        ") " .
        "VALUES " .
        "$sql_values_lots"
    );

    $logs['lots']['success'] = true;

    // Bets
    try {
      // Bets Values to String
      $sql_values_bets = implode(', ', $sql_values_bets);

      mysqli_query(
        $con,
        "INSERT INTO " .
          "bets (price, user_id, lot_id) " .
          "VALUES " .
          "$sql_values_bets"
      );

      $logs['bets']['success'] = true;
    } catch (\Throwable $th) {
      $logs['bets']['error'] = mysqli_error($con);
    }
  } catch (\Throwable $th) {
    $logs['lots']['error'] = mysqli_error($con);
  }
} catch (\Throwable $th) {
  $logs['users']['error'] = mysqli_error($con);
}



echo '<h2>Seed</h2>';
foreach ($logs as $key => $log) {
  if (is_array($log) && isset($log['error'])) {
    echo '<p style="color: orange;">Database Table «' . $key . '» was not seeded due to an error: ' . $log['error'] . '</p>';
  } else {
    echo '<p style="color: lightgreen;">Database Table «' . $key . '» was successfully seeded!</p>';
  }
}
