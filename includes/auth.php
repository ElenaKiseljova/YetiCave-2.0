<?php
require_once 'includes/set.php';
require_once 'controllers/UserController.php';

session_start();

// Auth User Flag
$isAuth = false;

// User ID
$userId = null;

// User Name
$userName = null;

if (isset($_SESSION['user'])) {
  $userCon = new UserController($con);

  ['data' => $user] = $userCon->getBy('id', $_SESSION['user']['id']);

  if (!$user) {
    header('Location: /logout');
  } else {
    $isAuth = true;

    // Set user variables
    $userId = $user['id'];
    $userName = $user['name'];
  }
}
