<?php
// Source: queries.sql

require_once $_SERVER['DOCUMENT_ROOT'] . '/env/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/DBController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/CategoryController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/UserController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/LotController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/BetController.php';

// when installed via composer
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// Connect with database
$dbCon = new DBController();

$con = $dbCon->connect();

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

// Seeds Tabels

$USERS_COUNT = 7;

// Logs
$logs = [
  'categories' => [],
  'users' => [],
  'lots' => [],
  'bets' => []
];

$categoryCon = new CategoryController($con);
$userCon = new UserController($con);
$lotCon = new LotController($con);
$betCon = new BetController($con);

// Categories
$categories = [
  'boards' => 'Доски и лыжи',
  'attachment' => 'Крепления',
  'boots' => 'Ботинки',
  'clothing' => 'Одежда',
  'tools' => 'Инструменты',
  'other' => 'Разное'
];

foreach ($categories as $slug => $title) {
  ['error' => $error] = $categoryCon->create([$slug, $title]);

  if (isset($error)) {
    $logs['categories']['error'][$slug] = $error;
  }
}

// Users
foreach (range(1, $USERS_COUNT) as $userId) {
  $dataUser = [
    'name' => addslashes($faker->name()),
    'email' => $faker->email(),
    'password' => 'password',
    'contacts' => addslashes($faker->address()),
  ];

  ['error' => $error] = $userCon->create($dataUser, false);

  if (isset($error)) {
    $logs['users']['error'][$userId] = $error;
  }
}

// Lot ID (iterator)
$lotId = 0;

// Bet ID (iterator)
$betId = 0;

foreach (range(1, $USERS_COUNT) as $userId) {
  // Lots
  $lotsCount = mt_rand(1, 11);

  foreach (range(1, $lotsCount) as $lotKey) {
    // Lot ID
    $lotId++;

    $dataLot = [
      'slug' => $faker->slug(2),
      'title' => addslashes($faker->sentence(mt_rand(3, 6))),
      'image' => "lot-" . mt_rand(1, 6) . ".jpg",
      'description' => addslashes($faker->paragraph()),
      'price_start' => mt_rand(1000, 1000000),
      'price_step' => mt_rand(100, 1000),
      'expiration_date' => $faker->dateTimeBetween('-10 days', '+10 days')->format('Y-m-d H:i:s'),
      'category_id' => mt_rand(1, 6)
    ];

    ['error' => $error] = $lotCon->create($dataLot, $userId, false);

    if (isset($error)) {
      $logs['lots']['error'][$lotId] = $error;
    }

    // Bets
    $betsCount = mt_rand(0, 10);

    if ($betsCount > 0) {
      $price = $dataLot['price_start'];

      foreach (range(1, $betsCount) as $betKey) {
        // Bet ID
        $betId++;

        // Increase price
        $price += mt_rand(1, 10) * $dataLot['price_step'];

        // Random User
        $betUserId = mt_rand(2, $USERS_COUNT);
        $betUserId = $userId === $betUserId ? $betUserId - 1 : $betUserId;

        ['error' => $error] = $betCon->create($lotId, $price, $betUserId, false);

        if (isset($error)) {
          $logs['bets']['error'][$betId] = $error;
        } else if ($betKey === $betsCount && date_create($dataLot['expiration_date']) <= date_create()) {
          // Set winner for Lot
          ['error' => $error] = $lotCon->setWin($lotId, $betId, false);

          if (isset($error)) {
            $logs['bets']['error'][$betId] = $error;
          }
        }
      }
    }
  }
}

echo '<h2>Seed</h2>';
foreach ($logs as $key => $log) {
  if (is_array($log) && isset($log['error'])) {
    echo '<h3 style="color: orangered;">Database Table «' . $key . '» was not seeded due to an error: </h3>';

    foreach ($log['error'] as $key => $error) {
      echo '<p style="color: orange;">' . $error['message'] ?? '?' . '</p>';
    }
  } else {
    echo '<p style="color: mediumseagreen;">Database Table «' . $key . '» was successfully seeded!</p>';
  }
}
