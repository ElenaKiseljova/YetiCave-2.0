<?php
// Source: queries.sql

require_once $_SERVER['DOCUMENT_ROOT'] . '/env/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';

// when installed via composer
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// Connect with database
$db = new DBController();

$con = $db->connect();

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
  $sqlValuesUsers = [];
  foreach (range(1, $USERS_COUNT) as $key) {
    $cols = [
      'name' => addslashes($faker->name()),
      'email' => $faker->email(),
      'password' => password_hash('password', PASSWORD_DEFAULT),
      'address' => addslashes($faker->address()),
    ];

    foreach ($cols as $key => $col) {
      if (!is_int($col)) {
        $cols[$key] = "'" . $col . "'";
      }
    }

    $sqlValuesUsers[] = "(" . implode(', ', $cols)  . ")";
  }

  // Users Values to String
  $sqlValuesUsers = implode(', ', $sqlValuesUsers);

  mysqli_query(
    $con,
    "INSERT INTO " .
      "users (name, email, password, contacts) " .
      "VALUES " .
      "$sqlValuesUsers"
  );

  $logs['users']['success'] = true;

  // Lots
  try {
    // Lots Values Array
    $sqlValuesLots = [];

    // Bets Values Array
    $sqlValuesBets = [];

    foreach (range(1, $LOTS_COUNT) as $lotId) {
      $userId = mt_rand(1, $USERS_COUNT);
      $winnerId = mt_rand(0, 1) ? $faker->numberBetween(2, $USERS_COUNT) : "NULL";
      $winnerId = $winnerId === $userId ? $winnerId - 1 : $winnerId;

      $cols = [
        'slug' => $faker->slug(2),
        'title' => addslashes($faker->sentence(mt_rand(3, 6))),
        'image' => "lot-" . mt_rand(1, 6) . ".jpg",
        'description' => addslashes($faker->paragraph()),
        'price_start' => mt_rand(1000, 1000000),
        'price_step' => mt_rand(100, 1000),
        'expiration_date' => $faker->dateTimeBetween('-10 days', '+10 days')->format('Y-m-d H:i:s'),
        'category_id' => mt_rand(1, 6),
        'user_id' => $userId,
        'winner_id' => $winnerId,
      ];

      // Bets seed
      $price = $cols['price_start'];

      if ($lotId % 3) {
        foreach (range(1, $BETS_COUNT) as $key) {
          $price = $cols['price_start'] + mt_rand(1, 10) * $cols['price_step'];
          $betUserId = 0;

          if ($key === $BETS_COUNT && is_int($winnerId)) {
            $betUserId = $winnerId;
          } else {
            $betUserId = $faker->numberBetween(3, $USERS_COUNT);
            $betUserId = $winnerId === $betUserId ? $betUserId - 1 : $betUserId;
          }

          $sqlValuesBets[] = "(" . implode(', ', [$price, $betUserId, $lotId])  . ")";
        }
      }

      foreach ($cols as $key => $col) {
        if (!is_int($col) && $col !== 'NULL') {
          $cols[$key] = "'" . $col . "'";
        }
      }

      $sqlValuesLots[] = "(" . implode(', ', $cols)  . ")";
    }

    // Lots Values to String
    $sqlValuesLots = implode(', ', $sqlValuesLots);

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
        "$sqlValuesLots"
    );

    $logs['lots']['success'] = true;

    // Bets
    try {
      // Bets Values to String
      $sqlValuesBets = implode(', ', $sqlValuesBets);

      mysqli_query(
        $con,
        "INSERT INTO " .
          "bets (price, user_id, lot_id) " .
          "VALUES " .
          "$sqlValuesBets"
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
