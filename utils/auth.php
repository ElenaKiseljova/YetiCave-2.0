<?php
session_start();

// Auth User Flag
$isAuth = false;

// User Name
$userName = null;

if (isset($_SESSION['user'])) {
  $isAuth = true;

  $userId = $_SESSION['user']['id'];
  $userName = $_SESSION['user']['name'];
}
